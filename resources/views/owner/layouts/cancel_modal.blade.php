
<!-- Include Modal HTML -->
<div id="cancel-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Are you sure you want to cancel this booking?</p>
        <form id="cancel-form" method="POST">
            @csrf
            @method('POST')
            <button type="submit" class="btn btn-danger">Cancel Booking</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('cancel-modal');
        const closeBtn = modal.querySelector('.close');
        const cancelButtons = document.querySelectorAll('.open-cancel-dialog');
        const cancelForm = document.getElementById('cancel-form');

        cancelButtons.forEach(button => {
            button.addEventListener('click', () => {
                const bookingId = button.getAttribute('data-id');
                const actionUrl = `{{ route('owner.bookings.cancel', ':id') }}`.replace(':id', bookingId);
                cancelForm.setAttribute('action', actionUrl);
                modal.style.display = 'block';
            });
        });

        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>

<style>
    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 20% auto; /* Adjusted margin to center the modal */
        padding: 20px;
        border: 1px solid #888;
        width: 300px; /* Set a smaller width */
        max-width: 90%; /* Ensure it doesn't exceed the viewport width */
        border-radius: 5px; /* Optional: Rounded corners */
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 24px; /* Smaller close button */
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
