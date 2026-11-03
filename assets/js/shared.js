/* AJira Digital Club v4 — shared.js */
'use strict';

document.addEventListener('DOMContentLoaded', () => {

  /* SCROLL PROGRESS */
  const sp = document.createElement('div'); sp.id = 'sp';
  document.body.prepend(sp);
  window.addEventListener('scroll', () => {
    sp.style.transform = `scaleX(${Math.min(window.scrollY / (document.body.scrollHeight - innerHeight) || 0, 1)})`;
  }, { passive: true });

  /* NAVBAR */
  const nav = document.getElementById('navbar');
  window.addEventListener('scroll', () => nav?.classList.toggle('scrolled', scrollY > 20), { passive: true });

  /* NOTIFICATION DROPDOWN */
  const notificationToggle = document.getElementById('notificationToggle');
  const notificationDropdown = document.getElementById('notificationDropdown');
  const notificationMenu = notificationToggle?.closest('.notification-menu');
  const closeNotifications = () => {
    if (!notificationToggle || !notificationDropdown) return;
    notificationDropdown.hidden = true;
    notificationToggle.setAttribute('aria-expanded', 'false');
  };
  notificationToggle?.addEventListener('click', () => {
    if (!notificationDropdown) return;
    const opening = notificationDropdown.hidden;
    notificationDropdown.hidden = !opening;
    notificationToggle.setAttribute('aria-expanded', String(opening));
  });
  document.addEventListener('click', e => {
    if (notificationMenu && !notificationMenu.contains(e.target)) closeNotifications();
  });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && notificationDropdown && !notificationDropdown.hidden) {
      closeNotifications();
      notificationToggle?.focus();
    }
  });

  /* ACTIVE LINK */
  const cur = location.pathname.split('/').pop() || 'index.php';
  document.querySelectorAll('.nav-links a, .drawer-nav a').forEach(a => {
    if ((a.getAttribute('href') || '').split('/').pop() === cur) a.classList.add('active');
  });

  /* MOBILE DRAWER */
  const tog = document.getElementById('navTog'), drw = document.getElementById('drawer'), dx = document.getElementById('drwX');
  const openDrw  = () => { drw?.classList.add('open'); tog?.classList.add('open'); tog?.setAttribute('aria-expanded', 'true'); tog?.setAttribute('aria-label', 'Close menu'); document.body.style.overflow = 'hidden'; };
  const closeDrw = () => { drw?.classList.remove('open'); tog?.classList.remove('open'); tog?.setAttribute('aria-expanded', 'false'); tog?.setAttribute('aria-label', 'Open menu'); document.body.style.overflow = ''; };
  tog?.addEventListener('click', () => drw?.classList.contains('open') ? closeDrw() : openDrw());
  dx?.addEventListener('click', closeDrw);
  drw?.querySelector('.drawer-bg')?.addEventListener('click', closeDrw);
  document.querySelectorAll('.drawer-nav a').forEach(a => a.addEventListener('click', closeDrw));

  /* REVEAL */
  const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } }), { threshold: .08, rootMargin: '0px 0px -32px 0px' });
  document.querySelectorAll('.reveal,.reveal-l').forEach(el => io.observe(el));

  /* COUNTERS */
  const co = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { count(e.target); co.unobserve(e.target); } }), { threshold: .3 });
  document.querySelectorAll('.stat-num[data-n]').forEach(el => co.observe(el));
  function count(el) {
    const t = +el.dataset.n, s = el.dataset.s ?? '+', d = 1300, st = performance.now();
    const ease = p => 1 - Math.pow(1 - p, 3);
    const tick = now => {
      const p = Math.min((now - st) / d, 1);
      el.innerHTML = Math.floor(ease(p) * t).toLocaleString() + `<em>${s}</em>`;
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }

  /* LIGHTBOX */
  const lb = document.getElementById('lb'), lbImg = document.getElementById('lb-img');
  if (lb && lbImg) {
    const close = () => { lb.classList.remove('open'); document.body.style.overflow = ''; lbImg.src = ''; };
    document.querySelectorAll('[data-lb]').forEach(el => el.addEventListener('click', () => { lbImg.src = el.dataset.lb; lb.classList.add('open'); document.body.style.overflow = 'hidden'; }));
    document.getElementById('lb-close')?.addEventListener('click', close);
    lb.addEventListener('click', e => e.target === lb && close());
    document.addEventListener('keydown', e => e.key === 'Escape' && close());
  }

  /* TABS */
  document.querySelectorAll('.tab-group').forEach(g => {
    g.querySelectorAll('.tab-btn').forEach(b => b.addEventListener('click', function () {
      g.querySelectorAll('.tab-btn').forEach(x => x.classList.remove('active'));
      this.classList.add('active');
      g.querySelectorAll('.tab-pane').forEach(p => { p.style.display = p.id === this.dataset.tab ? (p.dataset.d || 'block') : 'none'; });
    }));
  });

  /* PROJECTS */
  const projectGrid = document.getElementById('projects-grid');
  const projectDialog = document.getElementById('project-dialog');
  if (projectGrid && projectDialog) {
    const cards = [...projectGrid.querySelectorAll('[data-project-card]')];
    const search = document.getElementById('project-search');
    const typeFilter = document.getElementById('project-type-filter');
    const statusFilter = document.getElementById('project-status-filter');
    const reset = document.getElementById('project-reset');
    const resultCount = document.getElementById('project-result-count');
    const emptyState = document.getElementById('project-empty');
    const dialogImage = document.getElementById('project-dialog-image');
    const dialogTitle = document.getElementById('project-dialog-title');
    const dialogType = document.getElementById('project-dialog-type');
    const dialogStatus = document.getElementById('project-dialog-status');
    const dialogDescription = document.getElementById('project-dialog-description');
    const dialogTools = document.getElementById('project-dialog-tools');

    const updateProjects = () => {
      const query = search.value.trim().toLocaleLowerCase();
      const type = typeFilter.value;
      const status = statusFilter.value;
      let visible = 0;

      cards.forEach(card => {
        const matches = (!query || card.textContent.toLocaleLowerCase().includes(query))
          && (!type || card.dataset.projectType === type)
          && (!status || card.dataset.projectStatus === status);
        card.hidden = !matches;
        if (matches) visible++;
      });

      resultCount.textContent = `${visible} ${visible === 1 ? 'project' : 'projects'}`;
      emptyState.hidden = visible !== 0;
    };

    search.addEventListener('input', updateProjects);
    typeFilter.addEventListener('change', updateProjects);
    statusFilter.addEventListener('change', updateProjects);
    reset.addEventListener('click', () => {
      search.value = '';
      typeFilter.value = '';
      statusFilter.value = '';
      updateProjects();
      search.focus();
    });

    projectGrid.addEventListener('click', event => {
      const trigger = event.target.closest('.project-detail-trigger');
      if (!trigger) return;

      dialogImage.src = trigger.dataset.projectImage;
      dialogImage.alt = `${trigger.dataset.projectTitle} project`;
      dialogTitle.textContent = trigger.dataset.projectTitle;
      dialogType.textContent = trigger.dataset.projectType;
      dialogStatus.textContent = trigger.dataset.projectStatus;
      dialogDescription.textContent = trigger.dataset.projectDescription;
      dialogTools.textContent = trigger.dataset.projectTools;
      projectDialog.showModal();
    });

    projectDialog.addEventListener('click', event => {
      if (event.target === projectDialog) projectDialog.close();
    });
    projectDialog.addEventListener('close', () => dialogImage.removeAttribute('src'));
  }

  /* SMOOTH SCROLL */
  document.querySelectorAll('a[href^="#"]').forEach(a => a.addEventListener('click', e => {
    const t = document.querySelector(a.getAttribute('href'));
    if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
  }));

  /* TOAST */
  window.toast = (msg, type = 'ok') => {
    let w = document.getElementById('_toast');
    if (!w) { w = document.createElement('div'); w.id = '_toast'; w.style.cssText = 'position:fixed;bottom:5.5rem;left:1rem;right:1rem;max-width:320px;margin:0 auto;z-index:9998;display:flex;flex-direction:column;gap:.45rem;pointer-events:none'; document.body.appendChild(w); }
    const el = document.createElement('div');
    const bgs = { ok: 'var(--forest)', err: '#991B1B', info: 'var(--green-pop)' };
    el.style.cssText = `background:${bgs[type]||bgs.ok};color:white;padding:.65rem 1rem;border-radius:8px;font-size:.82rem;font-weight:600;box-shadow:var(--sh-lg);animation:fade-up .25s ease;pointer-events:all;border-left:3px solid var(--gold)`;
    el.textContent = msg;
    w.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = '.25s'; setTimeout(() => el.remove(), 250); }, 3200);
  };

  /* CHATBOT */
  const Q = {
    join:        'To join, tap <a href="join.php" style="color:var(--gold-2);font-weight:700">Join Now</a> in the menu and complete the registration form. An admin will review and your membership will be aproved as soon as possible😊.',
    meetings: 'The club is always having meetings every Thursdays virtually(online) at 8 to 9 PM.',
    projects: 'We have a number of projects that members can work on. Check the <a href="projects.php" style="color:var(--gold-2);font-weight:700">Projects page</a> for the full list.',
    pay:         'Membership registration is handled by the club administrator. Complete the Join form and contact the admin if you have questions.',
    partnerships: 'We partner with local and international in doing various projects and events. Check the <a href="partnerships.php" style="color:var(--gold-2);font-weight:700">Partnerships page</a> for the full list.',
    history:     'The club was founded in 2020 by a group of students at Zetech University. Since then, we have grown to over 500 members and have hosted numerous events and workshops have a broader of us in our about page <a href="about.php" style="color:var(--gold-2);font-weight:700">About Us</a>.',
    events:      'We run free workshops, bootcamps and meetups every semester. Check the <a href="events.php" style="color:var(--gold-2);font-weight:700">Events page</a> for the full schedule. WhatsApp group gets notices first.',
    skills:      'We teach <strong>Web Development, Virtual assistance, Graphic Design, Digital Marketing, Content Creation, Data Annotation , Data Analysis ,Claud computing </strong> and <strong>Freelancing</strong>. Everything is hands-on — you build real projects.',
    freelancing: 'We coach you to land clients on <strong>Upwork, Fiverr, and local platforms</strong>. Many members earn from freelancing while still on campus.',
    cert:        'Yes! You earn a certificate for every training  you complete — real value for your CV, LinkedIn and your career development.',
    team:        'The club is led by <strong>Antoney Ouko</strong> (Chairperson). Meet the full exec on the <a href="team.php" style="color:var(--gold-2);font-weight:700">Team page</a>.',
    contact:     'Reach us via the <a href="contact.php" style="color:var(--gold-2);font-weight:700">Contact page</a> or join the <a href="#" style="color:var(--gold-2);font-weight:700">WhatsApp group</a> for instant replies.',
    location:    'We are at <strong>Zetech University, Ruiru and Mangu Campus</strong>, Kiambu County. Sessions happen on campus and online via Zoom.',
    hello:       'Hey there 👋 I\'m the AJira bot. Ask me anything — membership, events, payment, skills, freelancing, or the team.',
    thanks:      'You\'re welcome! Anything else I can help with? Don\'t forget to <a href="join.php" style="color:var(--gold-2);font-weight:700">join the club</a> if you haven\'t yet.',
    default:     'Good question. I can help with: <strong>joining, payment, events, skills, certificates, freelancing,</strong> or <strong>getting in touch</strong>. Try asking about one of those.',
  };

  function match(m) {
    const t = m.toLowerCase();
    if (/^(hi|hey|hello|hii|habari|sasa|sup|how are you)\b/i.test(t)) return Q.hello;
    if (/thank|asante/i.test(t))          return Q.thanks;
    if (/meet| meeting/i.test(t))        return Q.meetings;
    if (/join|register|signup|sign up/i.test(t)) return Q.join;
    if (/history|about|founder|who/i.test(t)) return Q.history;
    if (/projects/i.test(t)) return Q.projects;
    if (/partnerships/i.test(t)) return Q.partnerships;
    if (/pay|mpesa|m-pesa|till|paybill|cost|price|fee|ksh|kes|100|50|bei/i.test(t)) return Q.pay;
    if (/event|workshop|bootcamp|meetup|session|training|calendar/i.test(t)) return Q.events;
    if (/skill|learn|teach|course|web|design|market|content|code/i.test(t)) return Q.skills;
    if (/freelan|client|fiverr|upwork|earn|income|money|pesa/i.test(t))     return Q.freelancing;
    if (/cert|certif|badge/i.test(t))     return Q.cert;
    if (/team|chair|exec|leader|antoney/i.test(t)) return Q.team;
    if (/contact|reach|email|whatsapp|phone|call/i.test(t)) return Q.contact;
    if (/where|location|campus|ruiru|Mangu|zetech|place/i.test(t)) return Q.location;
    return Q.default;
  }

  const btn = document.getElementById('cb-btn'), box = document.getElementById('cb-box');
  const msgs = document.getElementById('cb-msgs'), inp = document.getElementById('cb-inp'), snd = document.getElementById('cb-snd'), cx = document.getElementById('cb-x');
  let open = false;

  const addMsg = (text, who) => {
    const el = document.createElement('div'); el.className = `cb-msg ${who}`; el.innerHTML = text;
    msgs?.appendChild(el); if (msgs) msgs.scrollTop = msgs.scrollHeight;
  };
  const typing = () => { const t = document.createElement('div'); t.className = 'cb-typing'; t.id = 'cb-typ'; t.innerHTML = '<span></span><span></span><span></span>'; msgs?.appendChild(t); if (msgs) msgs.scrollTop = msgs.scrollHeight; };
  const rmTyp  = () => document.getElementById('cb-typ')?.remove();
  const reply  = m => { typing(); setTimeout(() => { rmTyp(); addMsg(match(m), 'bot'); }, 800 + Math.random() * 400); };

  btn?.addEventListener('click', () => {
    open = !open; box?.classList.toggle('open', open); btn.querySelector('.dot')?.remove();
    if (open && msgs && !msgs.children.length) setTimeout(() => addMsg(Q.hello, 'bot'), 350);
  });
  cx?.addEventListener('click', () => { open = false; box?.classList.remove('open'); });
  const send = () => { const v = inp?.value.trim(); if (!v) return; addMsg(v, 'usr'); inp.value = ''; reply(v); };
  snd?.addEventListener('click', send);
  inp?.addEventListener('keydown', e => e.key === 'Enter' && send());
  document.querySelectorAll('.cb-q').forEach(q => q.addEventListener('click', () => { addMsg(q.textContent, 'usr'); reply(q.textContent); }));

  /* PHONE FORMAT */
  document.querySelectorAll('input[name="phone"]').forEach(i => i.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, '');
    if (v.startsWith('254')) v = '0' + v.slice(3);
    if (v.length > 10) v = v.slice(0, 10);
    this.value = v;
  }));
  document.querySelectorAll('input[name="reg_number"]').forEach(i => i.addEventListener('input', function () { this.value = this.value.toUpperCase(); }));

});
