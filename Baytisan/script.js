document.addEventListener('DOMContentLoaded', function() {
  // Set footer year
  const y = document.getElementById('year');
  if (y) y.textContent = new Date().getFullYear();

  // Modal & tabs
  const modal = document.getElementById('modal');

  // Modal for account btn
  const accountBtn = document.getElementById('accountBtn');

  const closeModal = document.getElementById('closeModal');
  const tabBtns = document.querySelectorAll('.tab-btn');
  const authForms = document.querySelectorAll('.auth-form');

  function openAuthModal(target='loginForm') {
    if (!modal) return;
    modal.classList.add('active');
    modal.setAttribute('aria-hidden','false');
    tabBtns.forEach(b => b.classList.remove('active'));
    authForms.forEach(f => f.classList.remove('active'));
    const tb = Array.from(tabBtns).find(b => b.dataset.target === target);
    if (tb) tb.classList.add('active');
    const tf = document.getElementById(target);
    if (tf) tf.classList.add('active');
  }
  function closeAuthModal() {
    if (!modal) return;
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden','true');
  }

  if (accountBtn) accountBtn.addEventListener('click', () => openAuthModal('loginForm'));
  if (closeModal) closeModal.addEventListener('click', closeAuthModal);
  if (modal) modal.addEventListener('click', e => { if (e.target === modal) closeAuthModal(); });

  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      tabBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const target = btn.dataset.target;
      authForms.forEach(f => f.classList.remove('active'));
      const el = document.getElementById(target);
      if (el) el.classList.add('active');
    });
  });

  // util: POST form URLSearchParams -> JSON
  async function postJSON(url, data) {
    const params = new URLSearchParams();
    for (const k in data) params.append(k, data[k]);
    const res = await fetch(url, { method: 'POST', body: params });
    return res.json();
  }

  // AUTH: signup
  const signupSubmit = document.getElementById('signupSubmit');
  if (signupSubmit) {
    signupSubmit.addEventListener('click', async () => {
      const first = document.getElementById('signup-firstname').value.trim();
      const last = document.getElementById('signup-lastname').value.trim();
      const email = document.getElementById('signup-email').value.trim();
      const pass = document.getElementById('signup-password').value;
      const msg = document.getElementById('signupMsg');
      msg.textContent = 'Creating account...';
      try {
        const res = await postJSON('signup.php', { first_name: first, last_name: last, email: email, password: pass });
        if (res.ok) {
          msg.textContent = 'Account created. Redirecting...';
          location.reload();
        } else msg.textContent = res.msg || 'Error';
      } catch (e) { msg.textContent = 'Network error'; }
    });
  }

  // AUTH: login (customer)
  const loginSubmit = document.getElementById('loginSubmit');
  if (loginSubmit) {
    loginSubmit.addEventListener('click', async () => {
      const email = document.getElementById('login-email').value.trim();
      const pass = document.getElementById('login-password').value;
      const msg = document.getElementById('loginMsg');
      msg.textContent = 'Signing in...';
      try {
        const res = await postJSON('login.php', { email: email, password: pass });
        if (res.ok) {
          msg.textContent = 'Logged in. Reloading...';
          location.reload();
        } else msg.textContent = res.msg || 'Invalid';
      } catch (e) { msg.textContent = 'Network error'; }
    });
  }

  /*
    i commented it out; reason: irrelevant as admin_login.php and admin register.php_already handles it,
    also its not connected to anything. probably
    -joshua xd

    // AUTH: admin login
    const adminSubmit = document.getElementById('adminSubmit');
    if (adminSubmit) {
      adminSubmit.addEventListener('click', async () => {
        const email = document.getElementById('admin-email').value.trim();
        const pass = document.getElementById('admin-password').value;
        const msg = document.getElementById('adminMsg');
        msg.textContent = 'Signing in...';
        try {
          const res = await postJSON('admin_login.php', { email: email, password: pass });
          if (res.ok && res.user && res.user.role === 'admin') {
            msg.textContent = 'Welcome admin. Redirecting...';
            window.location.href = 'admin_dashboard.php';
          } else if (res.ok) {
            msg.textContent = 'Account is not admin';
          } else msg.textContent = res.msg || 'Invalid';
        } catch (e) { msg.textContent = 'Network error'; }
      });
    }
      

    // Admin Reg
    const adminRegisterSubmit = document.getElementById('adminRegisterSubmit');
    if (adminRegisterSubmit) {
      adminRegisterSubmit.addEventListener('click', async () => {
        const first = document.getElementById('admin-first').value.trim();
        const last = document.getElementById('admin-last').value.trim();
        const email = document.getElementById('admin-email-reg').value.trim();
        const pass = document.getElementById('admin-pass').value;
        const confirm = document.getElementById('admin-confirm').value;
        const msg = document.getElementById('adminMsg');
        msg.textContent = 'Registering admin...';
        try {
          const res = await postJSON('admin_register.php', { 
            first_name: first, last_name: last, email: email, 
            password: pass, confirm_password: confirm 
          });
          if (res.ok) {
            msg.textContent = 'Admin registered. Redirecting...';
            window.location.href = 'admin_login.php';
          } else msg.textContent = res.msg || 'Error';
        } catch (e) { msg.textContent = 'Network error'; }
      });
    }
  
  */
});