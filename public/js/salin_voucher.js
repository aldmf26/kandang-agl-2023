function copyToClipboard(text) {
    const el = document.createElement('textarea');
    el.value = text;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    // Optional: Menampilkan pesan sukses
    Toastify({
        text: `Kode Voucher ${el.value} disalin`,
        duration: 3000,
        style: {
            background: "#EAF7EE",
            color: "#7F8B8B"
        },
        close: true,
        avatar: "https://cdn-icons-png.flaticon.com/512/190/190411.png"
    }).showToast();
}