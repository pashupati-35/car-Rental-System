function openModal(url, title) {
    console.log('Opening modal with URL:', url); // Debug log
    $('#ownerModalLabel').text(title);
    $('#ownerModalBody').load(url, function(response, status, xhr) {
        if (status === "success") {
            console.log('Modal content loaded'); // Debug log
            $('#ownerModal').modal('show');
        } else {
            console.log('Failed to load modal content:', xhr.statusText); // Debug log
        }
    });
}
