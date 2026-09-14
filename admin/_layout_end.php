  </div>
</main>
</div>

<button class="sb-tog" id="sbt" aria-label="Toggle sidebar"><i class="bi bi-list" id="sbti"></i></button>

<script>
const sb=document.getElementById('sb'),sbt=document.getElementById('sbt'),sbti=document.getElementById('sbti');
let op=false;
sbt?.addEventListener('click',()=>{
  op=!op; sb?.classList.toggle('mo',op);
  sbti.className='bi bi-'+(op?'x-lg':'list');
});
document.addEventListener('click',e=>{
  if(op&&!sb?.contains(e.target)&&!sbt?.contains(e.target)){op=false;sb?.classList.remove('mo');sbti.className='bi bi-list';}
});
</script>
<script src="../assets/js/shared.js"></script>
</body>
</html>
