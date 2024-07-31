$(document).ready(function() {
  $("button.btn1").on("click", function(event) {
      event.preventDefault();

      var requiredFilled = true;
      $("#addStudentForm input, #addStudentForm select").each(function() {
          if ($(this).prop("required") && $(this).val() === "") {
              requiredFilled = false;
              $(this).addClass("is-invalid");
          } else {
              $(this).removeClass("is-invalid");
          }
      });

      if (requiredFilled) {
          $.ajax({
              url: "function.php",
              type: "POST",
              data: $("#addStudentForm").serialize() + "&save=true",
              success: function(response) {
                  try {
                      response = JSON.parse(response);
                  } catch (e) {
                      Swal.fire({
                          title: "Error!",
                          text: "Failed to parse JSON response: " + e,
                          icon: "error"
                      });
                      return;
                  }

                  if (response.success) {
                      Swal.fire({
                          icon: "success",
                          title: "Form Submitted",
                          showConfirmButton: true
                      }).then(() => {
                          generateQRCode(response.studentId, response.studentName);
                      });
                  } else if (response.error === "Student already exists") {
                      toastr.error("This student already exists. Please check the details and try again.");
                  } else {
                      toastr.error("Verify your Entry: " + response.error);
                  }
              }
          });
      } else {
          toastr.error("Please fill out all required fields.");
      }
  });

  function generateQRCode(studentId, studentName) {
      $.ajax({
          type: "POST",
          url: "generate.php",
          data: { studentId: studentId, studentName: studentName },
          success: function(response) {
              try {
                  response = JSON.parse(response);
              } catch (e) {
                  Swal.fire({
                      title: "Error!",
                      text: "Failed to parse JSON response: " + e,
                      icon: "error"
                  });
                  return;
              }

              if (response.success) {
                  $('#qrCodeContainer').html('<img id="qrCodeImage" src="' + response.qrCodeUrl + '" alt="QR Code">');
                  $('#downloadLink').attr('href', response.qrCodeUrl);
                  $('#downloadLink').attr('download', response.fileName);
                  $('#downloadLink').show();
              } else {
                  Swal.fire({
                      title: "Error!",
                      text: "There was an error generating the QR code: " + (response.error || "Unknown error"),
                      icon: "error"
                  });
              }
          },
          error: function(xhr, status, error) {
              Swal.fire({
                  title: "Error!",
                  text: "AJAX error: " + error,
                  icon: "error"
              });
          }
      });
  }
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



    
  document.addEventListener('DOMContentLoaded', function () {
    var currentLocation = location.href;
    var menuItem = document.querySelectorAll('#sidebarNav .nav-link');
    var menuLength = menuItem.length;
    for (var i = 0; i < menuLength; i++) {
      if (menuItem[i].href === currentLocation) {
        menuItem[i].classList.add('active');
        if (menuItem[i].closest('.nav-treeview')) {
          menuItem[i].closest('.nav-treeview').parentNode.querySelector('.nav-link').classList.add('active');
        }
      }
    }
  });
