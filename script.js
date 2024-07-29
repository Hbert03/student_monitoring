function generateQRCode(studentId, studentName) {
    $.ajax({
      type: 'POST',
      url: 'generate.php',
      data: { studentId: studentId, studentName: studentName },
      success: function(response) {
        // Parse the JSON response
        try {
          response = JSON.parse(response);
        } catch (e) {
          Swal.fire({
            title: 'Error!',
            text: 'Failed to parse JSON response: ' + e,
            icon: 'error'
          });
          return;
        }
  
        if (response.success) {
          $('#qrCodeContainer').html('<img id="qrCodeImage" src="' + response.qrCodeUrl + '" alt="QR Code">');
          $('#downloadLink').attr('href', response.qrCodeUrl);
          $('#downloadLink').show();
        } else {
          Swal.fire({
            title: 'Error!',
            text: 'There was an error generating the QR code: ' + (response.error || 'Unknown error'),
            icon: 'error'
          });
        }
      },
      error: function(xhr, status, error) {
        Swal.fire({
          title: 'Error!',
          text: 'AJAX error: ' + error,
          icon: 'error'
        });
      }
    });
  }
  
  $(document).ready(function() {
    $("button.btn1").on("click", function(event) {
      event.preventDefault();
  
      $("input").removeClass("invalid");
  
      let isValid = true;
  
      $("#addStudentForm").find("input[required], select[required]").each(function() {
        if ($(this).val() === "") {
          $(this).addClass("invalid");
          isValid = false;
        }
      });
  
      if (isValid) {
        $.ajax({
          url: "function.php",
          type: "POST",
          data: $("#addStudentForm").serialize() + "&save=true",
          success: function(response) {
            response = JSON.parse(response);
            if (response.success) {
              Swal.fire({
                icon: "success",
                title: "Form Submitted",
                showConfirmButton: true
              }).then(() => {
                generateQRCode(response.studentId, response.studentName);
              });
            } else {
              toastr.error("Verify your Entry: " + response.error);
            }
          }
        });
      } else {
        toastr.error("Please fill out all required fields.");
      }
    });
  });
  



$(document).ready(function() {
  $('select.gradelevel').select2({
    theme: "bootstrap4",
    placeholder: 'Select Grade level',
    ajax: {
        url: 'select_fetch.php',
        type: 'post',
        dataType: "json",
        delay: 250, 
        data: function(params) {
            return {
                gradelevel: true,
                term: params.term
            };
        },
        processResults: function(data) {
            return {
                results: $.map(data.results, function(grade) {
                    return {
                        id: grade.grade_level,
                        text: grade.grade_level_name
                    };
                })
            };
        },
        cache: true
    },
    minimumInputLength: 0,
    allowClear: true
  })
  })