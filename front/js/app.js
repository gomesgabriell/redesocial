/*
  Front-end Conecta
  API esperada: ../back/api.php?rota=usuario|publicacao|curtida
  O back-end descrito até aqui ainda não possui rota de autenticação.
  Para não comparar senhas no navegador, o login aguarda o endpoint PHP ?rota=login.
*/
const API_URL = "../back/api.php";
const $ = (selector) => document.querySelector(selector);

const state = {
  user: JSON.parse(localStorage.getItem("conecta_user") || "null"),
  posts: [],
  likes: []
};

function showToast(message) {
  const toast = $("#toast");
  toast.textContent = message;
  toast.classList.add("show");
  window.setTimeout(() => toast.classList.remove("show"), 3000);
}
function setMessage(selector, message, success=false) {
  const el = $(selector);
  el.textContent = message;
  el.style.color = success ? "#23845b" : "";
}
function escapeHtml(value="") {
  return String(value).replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));
}
async function api(route, method="GET", data=null) {
  const options = {method, headers: {"Accept":"application/json"}};
  if (data !== null) {
    options.headers["Content-Type"] = "application/json";
    options.body = JSON.stringify(data);
  }
  const response = await fetch(`${API_URL}?rota=${encodeURIComponent(route)}`, options);
  const raw = await response.text();
  let result;
  try { result = raw ? JSON.parse(raw) : {}; }
  catch { throw new Error(`O servidor respondeu algo que não é JSON (HTTP ${response.status}). Confira o PHP e o caminho da API.`); }
  if (!response.ok) throw new Error(result.erro || result.mensagem || `Erro HTTP ${response.status}`);
  if (result && result.erro) throw new Error(result.erro);
  return result;
}

function switchAuth(tab) {
  document.querySelectorAll("[data-auth-tab]").forEach(b => b.classList.toggle("active", b.dataset.authTab === tab));
  $("#loginForm").classList.toggle("hidden", tab !== "login");
  $("#registerForm").classList.toggle("hidden", tab !== "register");
  setMessage("#authMessage", "");
}
document.querySelectorAll("[data-auth-tab]").forEach(button => button.addEventListener("click", () => switchAuth(button.dataset.authTab)));

$("#registerForm").addEventListener("submit", async (event) => {
  event.preventDefault();
  setMessage("#authMessage", "Criando sua conta...");
  const data = {
    nome: $("#registerName").value.trim(),
    username: $("#registerUsername").value.trim().replace(/^@/,""),
    email: $("#registerEmail").value.trim(),
    senha: $("#registerPassword").value
  };
  try {
    await api("usuario", "POST", data);
    setMessage("#authMessage", "Cadastro realizado! Agora entre com seu e-mail e senha.", true);
    $("#loginEmail").value = data.email;
    $("#loginPassword").value = "";
    $("#registerForm").reset();
    switchAuth("login");
    setMessage("#authMessage", "Cadastro realizado! Faça seu login.", true);
  } catch (error) { setMessage("#authMessage", error.message); }
});

$("#loginForm").addEventListener("submit", async (event) => {
  event.preventDefault();
  setMessage("#authMessage", "Verificando acesso...");
  try {
    // Requer implementar no PHP uma rota POST ?rota=login que valide password_verify
    // e devolva { id_usuario, nome, username, email } sem devolver a senha.
    const result = await api("login", "POST", {
      email: $("#loginEmail").value.trim(),
      senha: $("#loginPassword").value
    });
    const user = result.usuario || result;
    if (!user.id_usuario) throw new Error("A rota de login não devolveu id_usuario.");
    state.user = {
      id_usuario: Number(user.id_usuario),
      nome: user.nome || "Usuário",
      username: user.username || "usuario",
      email: user.email || ""
    };
    localStorage.setItem("conecta_user", JSON.stringify(state.user));
    enterApp();
  } catch (error) {
    setMessage("#authMessage", error.message.includes("Rota não encontrada")
      ? "A rota de login ainda não existe no seu PHP. O cadastro já usa sua rota usuario; precisamos adicionar a rota login para autenticar com segurança."
      : error.message);
  }
});

function enterApp() {
  $("#authView").classList.add("hidden");
  $("#appView").classList.remove("hidden");
  updateUserUI();
  openView("feed");
  loadFeed();
}
function updateUserUI() {
  const user = state.user;
  if (!user) return;
  const initial = (user.nome || "U").trim().charAt(0).toUpperCase();
  $("#sideAvatar").textContent = $("#topAvatar").textContent = $("#profileAvatar").textContent = initial;
  $("#sideName").textContent = user.nome;
  $("#sideUsername").textContent = $("#topUsername").textContent = $("#profileUsername").textContent = "@" + user.username;
  $("#profileName").textContent = user.nome;
}
function logout() {
  localStorage.removeItem("conecta_user");
  state.user = null;
  $("#appView").classList.add("hidden");
  $("#authView").classList.remove("hidden");
  $("#loginForm").reset();
  setMessage("#authMessage", "Você saiu da conta.", true);
}
$("#logoutButton").addEventListener("click", logout);

function openView(name) {
  const titles = {feed:"Seu feed", create:"Criar publicação", profile:"Meu perfil"};
  document.querySelectorAll(".view-section").forEach(section => section.classList.add("hidden"));
  $(`#${name}Section`).classList.remove("hidden");
  document.querySelectorAll(".nav-link").forEach(button => button.classList.toggle("active", button.dataset.view === name));
  $("#pageTitle").textContent = titles[name];
  if (name === "profile") renderProfile();
}
document.querySelectorAll("[data-view]").forEach(button => button.addEventListener("click", () => openView(button.dataset.view)));
document.querySelectorAll("[data-go-create]").forEach(button => button.addEventListener("click", () => openView("create")));

async function loadFeed() {
  $("#feedList").innerHTML = '<div class="empty-state">Carregando publicações...</div>';
  try {
    const data = await api("publicacao");
    state.posts = Array.isArray(data) ? data : (data.publicacoes || []);
    // Curtidas são carregadas por publicação, pois o endpoint pode retornar todas ou filtrar por id_publicacao.
    const likeLists = await Promise.all(state.posts.map(post =>
      api(`curtida&id_publicacao=${encodeURIComponent(post.id_publicacao)}`).catch(() => [])
    ));
    state.likes = likeLists.flatMap(item => Array.isArray(item) ? item : []);
    renderPosts(state.posts, "#feedList");
    if (!$("#profileSection").classList.contains("hidden")) renderProfile();
  } catch (error) {
    $("#feedList").innerHTML = `<div class="empty-state"><strong>Não foi possível carregar o feed.</strong>${escapeHtml(error.message)}<br><br>Confira se o servidor PHP está iniciado e se a API está em ../back/api.php.</div>`;
  }
}
function getLikes(postId) {
  return state.likes.filter(like => Number(like.id_publicacao) === Number(postId));
}
function renderPosts(posts, target) {
  const container = $(target);
  if (!posts.length) {
    container.innerHTML = '<div class="empty-state"><strong>Seu feed está vazio por enquanto.</strong>Quando alguém publicar, o conteúdo aparecerá aqui.</div>';
    return;
  }
  const sorted = [...posts].sort((a,b) => new Date(b.data_publicacao || 0) - new Date(a.data_publicacao || 0));
  container.innerHTML = sorted.map(post => {
    const postId = Number(post.id_publicacao);
    const likes = getLikes(postId);
    const liked = likes.some(like => Number(like.id_usuario) === Number(state.user.id_usuario));
    const author = post.nome || post.username || `Usuário ${post.id_usuario}`;
    const image = post.imagem_url ? `<img class="post-image" src="${escapeHtml(post.imagem_url)}" alt="Imagem da publicação" loading="lazy" onerror="this.style.display='none'">` : '<div class="post-placeholder">✦</div>';
    const date = post.data_publicacao ? new Date(post.data_publicacao.replace(" ","T")).toLocaleString("pt-BR") : "";
    return `<article class="post-card">
      <div class="post-head"><div class="avatar">${escapeHtml(author.charAt(0).toUpperCase())}</div><div><div class="post-author">${escapeHtml(author)}</div><small class="post-date">${escapeHtml(date)}</small></div></div>
      ${image}
      <div class="post-body">
        <div class="post-actions"><button class="like-button ${liked ? "liked" : ""}" data-like="${postId}" aria-label="${liked ? "Descurtir" : "Curtir"}">${liked ? "♥" : "♡"} Curtir</button><span class="like-count">${likes.length} ${likes.length === 1 ? "curtida" : "curtidas"}</span></div>
        <p class="caption"><strong>${escapeHtml(author)}</strong>${escapeHtml(post.legenda || "")}</p>
      </div>
    </article>`;
  }).join("");
  container.querySelectorAll("[data-like]").forEach(button => button.addEventListener("click", () => toggleLike(Number(button.dataset.like))));
}
async function toggleLike(postId) {
  const existing = state.likes.find(like => Number(like.id_publicacao) === postId && Number(like.id_usuario) === Number(state.user.id_usuario));
  try {
    if (existing) {
      await api("curtida", "DELETE", {id_usuario:state.user.id_usuario, id_publicacao:postId});
    } else {
      await api("curtida", "POST", {id_usuario:state.user.id_usuario, id_publicacao:postId});
    }
    await loadFeed();
  } catch (error) { showToast(error.message); }
}

$("#postForm").addEventListener("submit", async (event) => {
  event.preventDefault();
  const data = {
    id_usuario: state.user.id_usuario,
    legenda: $("#postCaption").value.trim(),
    imagem_url: $("#postImage").value.trim() || null
  };
  if (!data.legenda) return setMessage("#postMessage", "Escreva uma legenda para publicar.");
  try {
    await api("publicacao", "POST", data);
    $("#postForm").reset();
    setMessage("#postMessage", "Publicação criada com sucesso!", true);
    await loadFeed();
    openView("feed");
    showToast("Sua publicação foi adicionada ao feed!");
  } catch (error) { setMessage("#postMessage", error.message); }
});

function renderProfile() {
  const mine = state.posts.filter(post => Number(post.id_usuario) === Number(state.user.id_usuario));
  $("#profilePostCount").textContent = mine.length;
  renderPosts(mine, "#profilePosts");
}

if (state.user && state.user.id_usuario) enterApp();
