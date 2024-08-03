

function confirmLogout() {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will be logged out!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("logoutForm").submit();
        }
    });
}


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
                          title: "Student Successfully Saved!",
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

  $("button.add_school_year").on("click", function() {
    Swal.fire({
        title: "Add School Year",
        html: "<input type='text' id='schoolYear' placeholder='School year' class='swal2-input'>",
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Save',
        preConfirm: () => {
            const schoolYear = Swal.getPopup().querySelector('#schoolYear').value;
            if (!schoolYear) {
                Swal.showValidationMessage(`Please enter the school year`);
                return false;
            }
            return { schoolYear: schoolYear };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'function.php',
                type: 'POST',
                data: { addschoolyear: true, school_year: result.value.schoolYear },
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to parse JSON response: ' + e
                        });
                        return;
                    }

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'School Year Saved',
                            text: 'The school year has been added successfully.'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to save school year'
                    });
                }
            });
        }
    });
});


//add subject
$("button.add_subject").on("click", function() {
    Swal.fire({
        title: "Add Subject",
        html: "<input type='text' id='subject' placeholder='Subject Name' class='swal2-input'>",
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Save',
        preConfirm: () => {
            const subject = Swal.getPopup().querySelector('#subject').value;
            if (!subject) {
                Swal.showValidationMessage(`Please enter the school year`);
                return false;
            }
            return { subject: subject };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'function.php',
                type: 'POST',
                data: { addsubject: true, subject: result.value.subject },
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to parse JSON response: ' + e
                        });
                        return;
                    }

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'School Year Saved',
                            text: 'The school year has been added successfully.'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to save school year'
                    });
                }
            });
        }
    });
});



  $(document).ready(function() {
    $("button.btn2").on("click", function(event) {
        event.preventDefault();
  
        var requiredFilled = true;
        $("#addteacherForm input, #addteacherForm select").each(function() {
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
                data: $("#addteacherForm").serialize() + "&save_teacher=true",
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
                            title: "Teacher Saved!",
                            showConfirmButton: true
                        }).then(() => {
                            generateQRCode(response.studentId, response.studentName);
                        });
                    }
                    else {
                        toastr.error("Already Exist");
                    }
                }
            });
        } else {
            toastr.error("Please fill out all required fields.");
        }
    });
  })  


  
$(document).ready(function() {
    $('select.grade_level1').select2({
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


    //subject
    $(document).ready(function() {
        $('select.subject').select2({
          theme: "bootstrap4",
          placeholder: 'Select Subject',
          ajax: {
              url: 'select_fetch.php',
              type: 'post',
              dataType: "json",
              delay: 250, 
              data: function(params) {
                  return {
                      subject: true,
                      term: params.term
                  };
              },
              processResults: function(data) {
                  return {
                      results: $.map(data.results, function(grade) {
                          return {
                              id: grade.subject_id,
                              text: grade.subject_name
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



//teacher
        $(document).ready(function() {
            $('select.teacher').select2({
              theme: "bootstrap4",
              placeholder: 'Select Teacher',
              ajax: {
                  url: 'select_fetch.php',
                  type: 'post',
                  dataType: "json",
                  delay: 250, 
                  data: function(params) {
                      return {
                          teacher: true,
                          term: params.term
                      };
                  },
                  processResults: function(data) {
                      return {
                          results: $.map(data.results, function(grade) {
                              return {
                                  id: grade.teacher_id,
                                  text: grade.teacher_name
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

            //section
            $(document).ready(function() {
                $('select.section').select2({
                  theme: "bootstrap4",
                  placeholder: 'Select Grade level',
                  ajax: {
                      url: 'select_fetch.php',
                      type: 'post',
                      dataType: "json",
                      delay: 250, 
                      data: function(params) {
                          return {
                              section: true,
                              term: params.term
                          };
                      },
                      processResults: function(data) {
                          return {
                              results: $.map(data.results, function(grade) {
                                  return {
                                      id: grade.section_id,
                                      text: grade.section_name
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

                //schoolyear
                $(document).ready(function() {
                    $('select.school_year').select2({
                      theme: "bootstrap4",
                      placeholder: 'Select School Year',
                      ajax: {
                          url: 'select_fetch.php',
                          type: 'post',
                          dataType: "json",
                          delay: 250, 
                          data: function(params) {
                              return {
                                school_year: true,
                                  term: params.term
                              };
                          },
                          processResults: function(data) {
                              return {
                                  results: $.map(data.results, function(grade) {
                                      return {
                                          id: grade.school_year_id,
                                          text: grade.school_year_name
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


                   
    


//add section
    $(document).ready(function() {
        $("button.btn3").on("click", function(event) {
            event.preventDefault();
      
            var requiredFilled = true;
            $("#addSectionForm input, #addSectionForm select").each(function() {
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
                    data: $("#addSectionForm").serialize() + "&addSection=true",
                    success: function(response) {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            Swal.fire({
                                title: "Error!",
                                text: "Failed",
                                icon: "error"
                            });
                            return;
                        }
      
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Section Save!",
                                showConfirmButton: true
                            })
                        } 
                         else {
                            toastr.error("Verify your Entry: " + response.error);
                        }
                    }
                });
            } else {
                toastr.error("Please fill out all required fields.");
            }
        });
    });


    //add class_sched
    $(document).ready(function() {
        $("button.btn4").on("click", function(event) {
            event.preventDefault();
      
            var requiredFilled = true;
            $("#addclassScheduleForm select").each(function() {
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
                    data: $("#addclassScheduleForm").serialize() + "&classSched=true",
                    success: function(response) {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            Swal.fire({
                                title: "Error!",
                                text: "Failed",
                                icon: "error"
                            });
                            return;
                        }
      
                        if (response.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Section Save!",
                                showConfirmButton: true
                            })
                        } 
                         else {
                            toastr.error("Verify your Entry: " + response.error);
                        }
                    }
                });
            } else {
                toastr.error("Please fill out all required fields.");
            }
        });
    });



// student
$(document).ready(function() {
    $('select.student').select2({
        theme: "bootstrap4",
        placeholder: 'Select Students',
        ajax: {
            url: 'select_fetch.php',
            type: 'post',
            dataType: "json",
            delay: 250,
            data: function(params) {
                return {
                    addStudent1: true,
                    term: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data.results, function(student) {
                        return {
                            id: student.student_id,
                            text: student.fullname
                        };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 0,
        allowClear: true,
        multiple: true // Enable multiple selection
    });

    $("button.btn5").on("click", function(event) {
        event.preventDefault();

        var requiredFilled = true;
        $("#addstudentSectionForm select").each(function() {
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
                data: $("#addstudentSectionForm").serialize() + "&addSection1=true",
                success: function(response) {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        Swal.fire({
                            title: "Error!",
                            text: "Failed",
                            icon: "error"
                        });
                        return;
                    }

                    if (response.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Section Save!",
                            showConfirmButton: true
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
    $('select.sort_grade_level').select2({
        theme: "bootstrap4",
        placeholder: 'Sort By Grade Level',
        ajax: {
            url: 'select_fetch.php',
            type: 'POST',
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
    });
    $(".select2-container").css("margin-bottom", "1em");
    // Initialize DataTable
    var table = $('#student').DataTable({
        serverSide: true,
        lengthChange: true,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: "fetch_data.php",
            type: "POST",
            data: function(d) {
                d.fetch = true;
                d.grade_level = $('#sort_grade_level').val();
            },
            error: function(xhr, error, thrown) {
                console.log("Ajax Failed: " + thrown);
            }
        },
        columns: [
            { "data": "fullname" },
            { "data": "student_mobile" },
            { "data": "student_address" },
            { 
                "data": "qrcode",
                "orderable": false,
                "searchable": false,
                "render": function(data, type, row) {
                    return data;
                }
            },
            {"data": null,
                "render": function(data, type, row){
                    return "<button class='btn btn-info btn-sm edit' data-student='"+row.student_id+"'>Edit</button>";
                }
            },
            {"data": null,
                "render": function(data, type, row){
                    return "<button class='btn btn-danger btn-sm delete' data-student='"+ row.student_id+"'>Delete</button>";
                }
            }
        ],
        drawCallback: function(){
            deletedstudent();
            editstudent();
        }
    });

   
    $('#sort_grade_level').on('change', function() {
        table.draw();
    });
});

function deletedstudent(){
    $('#student').on('click', 'button.delete', function(){
        let student_id = $(this).data('student');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete it?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'fetch_data.php',
                            type: 'POST',
                            data: {
                                deletestudent: true,
                                student_id: student_id
                            },
                            success: function(response) {
                                if (response.trim() === "Your data has been deleted.") {
                                    Swal.fire(
                                        'Deleted!',
                                        'File has been deleted successfully.',
                                        'success'
                                    );
                                    $('#student').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        'Failed to delete file.',
                                        'error'
                                    );
                                }
                            },
                        });
                    }
                });
            });
    }
 

   function editstudent() {
    $('#student').on('click', 'button.edit', function() {
        let student_id = $(this).data('student');
        $.ajax({
            url: 'fetch_data.php',
            type: 'POST',
            data: {
                getdatastudent: true,
                student_id: student_id
            },
            success: function(response) {
                if (response.trim() !== "") {
                    var data = JSON.parse(response);
                    Swal.fire({
                        title: 'Student Details ',
                        html: '<label>Firstname:</label>' +
                              '<input id="swal-input1" class="form-control mb-2" value="' + data[0].student_firstname + '">' +
                              '<label>Middlename:</label>' +
                              '<input id="swal-input2" class="form-control mb-2" value="' + data[0].student_middlename + '">' +
                              '<label>Lastname:</label>' +
                              '<input id="swal-input3" class="form-control mb-2" value="' + data[0].student_lastname + '">' +
                              '<label>Address:</label>' +
                              '<input id="swal-input4" class="form-control mb-2" value="' + data[0].student_address + '">' +
                              '<label>Status:</label>' +
                              '<input id="swal-input5" class="form-control mb-2" value="' + data[0].student_status + '">',
                        focusConfirm: false,
                        confirmButtonText: 'Update',
                        preConfirm: () => {
                            const value1 = document.getElementById('swal-input1').value;
                            const value2 = document.getElementById('swal-input2').value;
                            const value3 = document.getElementById('swal-input3').value;
                            const value4 = document.getElementById('swal-input4').value;
                            const value5 = document.getElementById('swal-input5').value;
                            return [value1, value2, value3, value4, value5];
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const [value1, value2, value3, value4, value5] = result.value;
                            $.ajax({
                                url: 'fetch_data.php',
                                type: 'POST',
                                data: {
                                    updatestudent: true,
                                    student_id: student_id,
                                    student_firstname: value1,
                                    student_middlename: value2,
                                    student_lastname: value3,
                                    student_address: value4,
                                    student_status: value5,
                                },
                                success: function(response) {
                                    if (response.trim() === "Updated Successfully") {
                                        Swal.fire(
                                            'Updated!',
                                            'File has been updated successfully.',
                                            'success'
                                        );
                                        $('#student').DataTable().ajax.reload(null, false);
                                    } else {
                                        Swal.fire(
                                            'Failed!',
                                            'Failed to update file.',
                                            'error'
                                        );
                                    }
                                },
                            });
                        }
                    });
                }
            },
        });
    });
    };




$(document).ready(function(){
 $('#teacher').DataTable({
        serverSide: true,
        lengthChange: true,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: "fetch_data.php",
            type: "POST",
            data: {fetch_teacher: true},
            error: function(xhr, error, thrown) {
                console.log("Ajax Failed: " + thrown);
            }
        },
        columns: [
            { "data": "fullname" },
            { "data": "teacher_address" },
            { "data": "teacher_mobile" },
            { 
               "data": "teacher_status"
            },
            {"data": null,
                "render": function(data, type, row){
                    return "<button class='btn btn-info btn-sm edit' data-teacher='"+row.teacher_id+"'>Edit</button>";
                }
            },
            {"data": null,
                "render": function(data, type, row){
                    return "<button class='btn btn-danger btn-sm delete' data-teacher='"+row.teacher_id+"'>Delete</button>";
                }
            }
        ],
        drawCallback: function(){
            deletedteacher();
            editteacher();
        }
    });
})


function deletedteacher(){
    $('#teacher').on('click', 'button.delete', function(){
        let teacher_id = $(this).data('teacher');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete it?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'fetch_data.php',
                            type: 'POST',
                            data: {
                                deleteteacher: true,
                                teacher_id: teacher_id
                            },
                            success: function(response) {
                                if (response.trim() === "Your data has been deleted.") {
                                    Swal.fire(
                                        'Deleted!',
                                        'File has been deleted successfully.',
                                        'success'
                                    );
                                    $('#teacher').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        'Failed to delete file.',
                                        'error'
                                    );
                                }
                            },
                        });
                    }
                });
            });
    }
 

   function editteacher() {
    $('#teacher').on('click', 'button.edit', function() {
        let teacher_id = $(this).data('teacher');
        $.ajax({
            url: 'fetch_data.php',
            type: 'POST',
            data: {
                getdatateacher: true,
                teacher_id: teacher_id
            },
            success: function(response) {
                if (response.trim() !== "") {
                    var data = JSON.parse(response);
                    Swal.fire({
                        title: 'Student Details ',
                        html: '<label>Firstname:</label>' +
                              '<input id="swal-input1" class="form-control mb-2" value="' + data[0].teacher_firstname + '">' +
                              '<label>Middlename:</label>' +
                              '<input id="swal-input2" class="form-control mb-2" value="' + data[0].teacher_middlename + '">' +
                              '<label>Lastname:</label>' +
                              '<input id="swal-input3" class="form-control mb-2" value="' + data[0].teacher_lastname + '">' +
                              '<label>Address:</label>' +
                              '<input id="swal-input4" class="form-control mb-2" value="' + data[0].teacher_address + '">' +
                              '<label>Mobile Number:</label>' +
                              '<input id="swal-input5" class="form-control mb-2" value="' + data[0].teacher_mobile + '">' +
                              '<label>Status:</label>' +
                              '<input id="swal-input6" class="form-control mb-2" value="' + data[0].teacher_status + '">',
                        focusConfirm: false,
                        confirmButtonText: 'Update',
                        preConfirm: () => {
                            const value1 = document.getElementById('swal-input1').value;
                            const value2 = document.getElementById('swal-input2').value;
                            const value3 = document.getElementById('swal-input3').value;
                            const value4 = document.getElementById('swal-input4').value;
                            const value5 = document.getElementById('swal-input5').value;
                            const value6 = document.getElementById('swal-input6').value;
                            return [value1, value2, value3, value4, value5, value6];
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const [value1, value2, value3, value4, value5, value6]= result.value;
                            $.ajax({
                                url: 'fetch_data.php',
                                type: 'POST',
                                data: {
                                    updateteacher: true,
                                    teacher_id: teacher_id,
                                    teacher_firstname: value1,
                                    teacher_middlename: value2,
                                    teacher_lastname: value3,
                                    teacher_address: value4,
                                    teacher_mobile: value5,
                                    teacher_status: value6,
                                },
                                success: function(response) {
                                    if (response.trim() === "Updated Successfully") {
                                        Swal.fire(
                                            'Updated!',
                                            'File has been updated successfully.',
                                            'success'
                                        );
                                        $('#teacher').DataTable().ajax.reload(null, false);
                                    } else {
                                        Swal.fire(
                                            'Failed!',
                                            'Failed to update file.',
                                            'error'
                                        );
                                    }
                                },
                            });
                        }
                    });
                }
            },
        });
    });
    };




$(document).ready(function(){
    $('#classSched').DataTable({
           serverSide: true,
           lengthChange: true,
           responsive: true,
           autoWidth: false,
           ajax: {
               url: "fetch_data.php",
               type: "POST",
               data: {fetch_classSched: true},
               error: function(xhr, error, thrown) {
                   console.log("Ajax Failed: " + thrown);
               }
           },
           columns: [
               { "data": "subject_name" },
               { "data": "teacher_name" },
               { "data": "section_name" },
               { 
                  "data": "school_year_name"
               },
               {"data": null,
                   "render": function(data, type, row){
                       return "<button class='btn btn-info btn-sm edit' data-classched='"+row.class_schedule_id+"'>Edit</button>";
                   }
               },
               {"data": null,
                   "render": function(data, type, row){
                       return "<button class='btn btn-danger btn-sm delete' data-classched='"+row.class_schedule_id+"'>Delete</button>";
                   }
               }
           ],
           drawCallback: function(){
            edit();
            deleted();
           }
       });
   })

   function deleted(){
    $('#classSched').on('click', 'button.delete', function(){
        let class_schedule_id = $(this).data('classched');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete it?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'fetch_data.php',
                            type: 'POST',
                            data: {
                                delete: true,
                                class_schedule_id: class_schedule_id
                            },
                            success: function(response) {
                                if (response.trim() === "Your data has been deleted.") {
                                    Swal.fire(
                                        'Deleted!',
                                        'File has been deleted successfully.',
                                        'success'
                                    );
                                    $('#classSched').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire(
                                        'Failed!',
                                        'Failed to delete file.',
                                        'error'
                                    );
                                }
                            },
                        });
                    }
                });
            });
    }
 

   function edit() {
    $('#classSched').on('click', 'button.edit', function() {
        let class_schedule_id = $(this).data('classched');
        $.ajax({
            url: 'fetch_data.php',
            type: 'POST',
            data: {
                getdata: true,
                class_schedule_id: class_schedule_id
            },
            success: function(response) {
                if (response.trim() !== "") {
                    var data = JSON.parse(response);

                    Swal.fire({
                        title: 'EDIT DETAILS',
                        html: '<label>Subject:</label>' +
                              '<select id="swal-select1" class="form-control mb-2 select2"></select>' +
                              '<label>Teacher:</label>' +
                              '<select id="swal-select2" class="form-control mb-2 select2"></select>' +
                              '<label>Section:</label>' +
                              '<select id="swal-select3" class="form-control mb-2 select2"></select>' +
                              '<label>School Year:</label>' +
                              '<select id="swal-select4" class="form-control mb-2 select2 school_year"></select>',
                        focusConfirm: false,
                        confirmButtonText: 'Update',
                        preConfirm: () => {
                            const subject_id = $('#swal-select1').val();
                            const teacher_id = $('#swal-select2').val();
                            const section_id = $('#swal-select3').val();
                            const school_year_id = $('#swal-select4').val();
                            return [subject_id, teacher_id, section_id, school_year_id];
                        },
                        didOpen: () => {
                            $('.select2').select2();

                            $('#swal-select1').select2({
                                theme: "bootstrap4",
                                placeholder: 'Select Subject',
                                ajax: {
                                    url: 'select_fetch.php',
                                    type: 'post',
                                    dataType: "json",
                                    delay: 250,
                                    data: function(params) {
                                        return {
                                            subject: true,
                                            term: params.term
                                        };
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: $.map(data.results, function(subject) {
                                                return {
                                                    id: subject.subject_id,
                                                    text: subject.subject_name
                                                };
                                            })
                                        };
                                    },
                                    cache: true
                                },
                                minimumInputLength: 0,
                                allowClear: true
                            });
                            $('#swal-select1').append(new Option(data.subject_name, data.subject_id, true, true));

                            $('#swal-select2').select2({
                                theme: "bootstrap4",
                                placeholder: 'Select Teacher',
                                ajax: {
                                    url: 'select_fetch.php',
                                    type: 'post',
                                    dataType: "json",
                                    delay: 250,
                                    data: function(params) {
                                        return {
                                            teacher: true,
                                            term: params.term
                                        };
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: $.map(data.results, function(teacher) {
                                                return {
                                                    id: teacher.teacher_id,
                                                    text: teacher.teacher_name
                                                };
                                            })
                                        };
                                    },
                                    cache: true
                                },
                                minimumInputLength: 0,
                                allowClear: true
                            });
                            $('#swal-select2').append(new Option(data.teacher_name, data.teacher_id, true, true));

                            $('#swal-select3').select2({
                                theme: "bootstrap4",
                                placeholder: 'Select Section',
                                ajax: {
                                    url: 'select_fetch.php',
                                    type: 'post',
                                    dataType: "json",
                                    delay: 250,
                                    data: function(params) {
                                        return {
                                            section: true,
                                            term: params.term
                                        };
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: $.map(data.results, function(section) {
                                                return {
                                                    id: section.section_id,
                                                    text: section.section_name
                                                };
                                            })
                                        };
                                    },
                                    cache: true
                                },
                                minimumInputLength: 0,
                                allowClear: true
                            });
                            $('#swal-select3').append(new Option(data.section_name, data.section_id, true, true));

                            $('#swal-select4').select2({
                                theme: "bootstrap4",
                                placeholder: 'Select School Year',
                                ajax: {
                                    url: 'select_fetch.php',
                                    type: 'post',
                                    dataType: "json",
                                    delay: 250,
                                    data: function(params) {
                                        return {
                                            school_year: true,
                                            term: params.term
                                        };
                                    },
                                    processResults: function(data) {
                                        return {
                                            results: $.map(data.results, function(school_year) {
                                                return {
                                                    id: school_year.school_year_id,
                                                    text: school_year.school_year_name
                                                };
                                            })
                                        };
                                    },
                                    cache: true
                                },
                                minimumInputLength: 0,
                                allowClear: true
                            });
                            $('#swal-select4').append(new Option(data.school_year_name, data.school_year_id, true, true));
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const [subject_id, teacher_id, section_id, school_year_id] = result.value;
                            $.ajax({
                                url: 'fetch_data.php',
                                type: 'POST',
                                data: {
                                    update: true,
                                    class_schedule_id: class_schedule_id,
                                    subject_id: subject_id,
                                    teacher_id: teacher_id,
                                    section_id: section_id,
                                    school_year_id: school_year_id
                                },
                                success: function(response) {
                                    if (response.trim() === "Updated Successfully") {
                                        Swal.fire(
                                            'Updated!',
                                            'File has been updated successfully.',
                                            'success'
                                        );
                                        $('#classSched').DataTable().ajax.reload(null, false);
                                    } else {
                                        Swal.fire(
                                            'Failed!',
                                            'Failed to update file.',
                                            'error'
                                        );
                                    }
                                },
                            });
                        }
                    });
                }
            },
        });
    });
}
