$(document).ready(function() {
    $('#addroleform').on('submit', function(e) {
        e.preventDefault(); 
        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('role.submit') }}", 
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Handle success
                if (response.success) {
                    alert(response.message);  // Show success message (you can use any alert style)
                    $('#addroleform')[0].reset();
                    $('#exampleModal').modal('hide');  
                }
            }
        });
    });
});
