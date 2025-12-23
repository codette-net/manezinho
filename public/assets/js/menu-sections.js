async function updateSection(id, field, value) {
  await fetch('/admin/menu/sections/update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ id, field, value })
  });
}

document.addEventListener('change', (e) => {
  const target = e.target;
  if (!target.matches('[data-inline]')) return;

  const tr = target.closest('tr');
  if (!tr) return;

  const id = tr.dataset.id;
  const field = target.dataset.inline;
  const value =
    target.type === 'checkbox'
      ? (target.checked ? 1 : 0)
      : target.value;

  updateSection(id, field, value);
});
