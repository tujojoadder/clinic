document.querySelectorAll('[data-confirm]').forEach(f=>f.addEventListener('submit',e=>{if(!confirm(f.dataset.confirm))e.preventDefault()}));document.querySelectorAll('[data-search]').forEach(e=>e.addEventListener('input',()=>document.querySelectorAll('tbody tr').forEach(r=>r.hidden=!r.textContent.toLowerCase().includes(e.value.toLowerCase()))));

document.querySelectorAll('[data-print]').forEach(b=>b.addEventListener('click',()=>window.print()));

document.getElementById('menuToggle')?.addEventListener('click',()=>document.body.classList.toggle('menu'));
