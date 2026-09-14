<?php $rootPath = $rootPath ?? './'; ?>

<div class="seal-bar">
  <div class="wrap"><div class="seal-inner">
    <div class="seal-item"><i class="bi bi-building-check"></i> Registered — Zetech University</div>
    <div class="seal-item"><i class="bi bi-patch-check-fill"></i> AJira Digital Kenya</div>
    <div class="seal-item"><i class="bi bi-shield-check"></i> Admin Managed</div>
    <div class="seal-item"><i class="bi bi-geo-alt-fill"></i> Ruiru, Kiambu County</div>
  </div></div>
</div>

<footer>
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <div class="foot-logo">
          <div class="foot-logo-mark">AD</div>
          <div><span class="foot-logo-name">AJira Digital Club</span><span class="foot-logo-sub">Zetech University · Est. 2021</span></div>
        </div>
        <p class="foot-desc">Building Kenya's next generation of digital professionals — one student, one skill, one opportunity at a time.</p>
        <div class="foot-socials">
          <a href="#" class="foot-soc" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
          <a href="#" class="foot-soc" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" class="foot-soc" aria-label="X"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="foot-soc" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="foot-soc" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
        </div>
      </div>
      <div>
        <div class="foot-head">Navigate</div>
        <ul class="foot-links">
          <li><a href="<?= $rootPath ?>index.php"><i class="bi bi-house"></i> Home</a></li>
          <li><a href="<?= $rootPath ?>about.php"><i class="bi bi-info-circle"></i> About Us</a></li>
          <li><a href="<?= $rootPath ?>team.php"><i class="bi bi-people"></i> Our Team</a></li>
          <li><a href="<?= $rootPath ?>events.php"><i class="bi bi-calendar-event"></i> Events</a></li>
          <li><a href="<?= $rootPath ?>projects.php"><i class="bi bi-kanban"></i> Projects</a></li>
          <li><a href="<?= $rootPath ?>gallery.php"><i class="bi bi-images"></i> Gallery</a></li>
        </ul>
      </div>
      <div>
        <div class="foot-head">Resources</div>
        <ul class="foot-links">
          <li><a href="<?= $rootPath ?>join.php"><i class="bi bi-lightning-charge"></i> Join Membership</a></li>
          <li><a href="<?= $rootPath ?>contact.php"><i class="bi bi-chat-dots"></i> Contact Us</a></li>
          <li><a href="#"><i class="bi bi-whatsapp"></i> WhatsApp Group</a></li>
          <li><a href="#"><i class="bi bi-globe"></i> Helptivo Portal</a></li>
          <li><a href="<?= $rootPath ?>admin/login.php"><i class="bi bi-shield-lock"></i> Admin</a></li>
        </ul>
      </div>
      <div>
        <div class="foot-head">Get in Touch</div>
        <ul class="foot-links">
          <li><a href="#"><i class="bi bi-geo-alt"></i> Zetech Univ, Ruiru</a></li>
          <li><a href="mailto:ajiraclub@zetech.ac.ke"><i class="bi bi-envelope"></i> ajiraclub@zetech.ac.ke</a></li>
          <li><a href="#"><i class="bi bi-clock"></i> Mon–Fri 8AM – 6PM</a></li>
        </ul>
        <div style="margin-top:1.5rem">
          <a href="<?= $rootPath ?>join.php" class="btn btn-gold btn-sm"><i class="bi bi-lightning-charge-fill"></i> Join Now</a>
        </div>
      </div>
    </div>
    <div class="foot-bottom">
      <span>© <?= date('Y') ?> AJira Digital Club, Zetech University. All rights reserved.</span>
      <span>Built by club members · <a href="<?= $rootPath ?>admin/login.php">Admin</a></span>
    </div>
  </div>
</footer>

<script src="<?= $rootPath ?>assets/js/shared.js"></script>
</body>
</html>
