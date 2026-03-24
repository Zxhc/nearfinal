
function switchTab(tab) {
    const currentPath = window.location.pathname;
    window.location.href = currentPath + "?tab=" + tab;
}


function confirmDelete(id, tab) {
    if (confirm("Are you sure you want to delete this?")) {
        const currentPath = window.location.pathname;
        window.location.href = currentPath + "?delete_id=" + id + "&tab=" + tab;
    }
}