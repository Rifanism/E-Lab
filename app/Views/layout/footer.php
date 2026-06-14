<?php 

?>
</div>

<div id="toast-container"></div>

<script>
function showToast(msg, type='info') {
  const icons = {info:'ℹ️', success:'✅', warn:'⚠️', error:'❌'};
  const c = document.getElementById('toast-container');
  const t = document.createElement('div');
  t.className = 'toast';
  t.innerHTML = `<span>${icons[type]||'ℹ️'}</span><span>${msg}</span>`;
  c.appendChild(t);
  setTimeout(() => { t.classList.add('toast-out'); setTimeout(()=>t.remove(), 240); }, 3400);
}

function openModal(id)  { document.getElementById(id)?.classList.add('open'); }
function closeModal(id) { document.getElementById(id)?.classList.remove('open'); }
document.querySelectorAll('.modal-bg').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

document.addEventListener('DOMContentLoaded', () => {
  const fl = document.getElementById('flash-msg');
  if (fl) showToast(fl.dataset.msg, fl.dataset.type || 'info');
});
</script>

<?php if (!empty($_SESSION['flash'])): ?>
<span id="flash-msg" data-msg="<?= htmlspecialchars($_SESSION['flash']['msg']) ?>" data-type="<?= $_SESSION['flash']['type'] ?>"></span>
<?php unset($_SESSION['flash']); endif; ?>

</body>
</html>
