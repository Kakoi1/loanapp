function fetchAccContent(accId,action) {
fetch('includes/viewUser.php?accId=' + accId+'&action='+action)
.then(response => response.text())
.then(data => {
document.getElementById("view").innerHTML = data;
document.getElementById("overlay2").style.display = "flex";
})
.catch(error => {
console.error('Error fetching note content:', error);
});
}
function showImage(src) {

var overlay = document.createElement("div");
overlay.id = "overlay";
overlay.innerHTML = "<div id='image-container'><img src='" + src + "'></div>";
document.body.appendChild(overlay);
overlay.addEventListener("click", function() {
    overlay.remove();
});
}
function closeAcc(){
    document.getElementById("overlay2").style.display = "none";
}
// $(document).ready(function(){
//     // Show popup when Submit button is clicked
//     $(document).on('click', '#submitRejection', function() {
//         $('#popupOverlay').fadeIn();
//         $('#popupOverlay').css('display', 'flex'); // You can use jQuery to set CSS properties as well
//     });
//     // Hide popup div and overlay when Cancel button is clicked
//     $('#cancelRejection').click(function() {
//         $('#popupOverlay').fadeOut();
//     });

//     // AJAX request to handle response to account request
//     $(document).on('click', '.responseButton', function() {
//         var $form = $(this).closest('form');
//         var userId = $form.find('input[name="userId"]').val();
//         var action = $(this).data('action');

//         $.ajax({
//             type: 'POST',
//             url: '../reqRespon.php',
//             data: { userId: userId, action: action },
//             success: function(response) {
//                 if (response.trim() === "Response Sent") {
//                     // Redirect to dashboard
//                     window.location.href = "dashboard.php";
//                 } else {
//                     // Alert the error message
//                     alert(response);
//                 }
//             }
//         });
//     });
// });
$(document).on('click', '#submitRejection', function() {
    $('#popupOverlay').fadeIn();
    document.getElementById("popupOverlay").style.display = "flex";
});

$(document).on('click', '#cancelRejection', function() {
    $('#popupOverlay').fadeOut();
});



$(document).ready(function(){
    $(document).on('click', '.responseButton', function() {
        var $form = $(this).closest('form');
        var userId = $form.find('input[name="userId"]').val();
        var email = $form.find('input[name="email"]').val();
        var action = $(this).data('action');
        var reason = $('#rejectionReason').val(); 
        document.getElementById("loadingOverlay").style.display = "flex";
        var data = {
            userId: userId,
            action: action,
            email: email,
            reason: reason 
        };

        $.ajax({
            type: 'POST',
            url: '../reqRespon.php',
            data: data,
            success: function(response) {
                $('#loadingOverlay').hide();
                setTimeout(function() {    
                alert(response);
                    window.location.href = "dashboard.php";
                }, 1000);
            }
        });
    });
});

// ============================================================
$(document).on('click', '#submitRejection1', function() {
    $('#popupOverlay1').fadeIn();
    document.getElementById("popupOverlay1").style.display = "flex";
});

$(document).on('click', '#cancelRejection1', function() {
    $('#popupOverlay1').fadeOut();
});

$(document).ready(function(){
    $(document).on('click', '.responseButton1', function() {
        var $form = $(this).closest('form');
        var userId = $form.find('input[name="userId"]').val();
        var reqId = $form.find('input[name="reqId"]').val();
        var loanId = $form.find('input[name="loanId"]').val();
        var amount = $form.find('input[name="amount"]').val();
        var term = $form.find('input[name="term"]').val();
        var email = $form.find('input[name="email"]').val();
        var action = $(this).data('action');
        var reason = $('#rejectionReason').val(); 
       document.getElementById("loadingOverlay").style.display = "flex";
        var data = {
            userId: userId,
            reqId: reqId,
            action: action,
            amount: amount,
            term: term,
            loanId: loanId,
            email: email,
            reason: reason 
        };

        $.ajax({
            type: 'POST',
            url: '../requestLoan.php',
            data: data,
            success: function(response) {  
                $('#loadingOverlay').hide();
                    setTimeout(function() {
                        
                        alert(response);
                        window.location.href = "dashboard.php";
                    }, 1000);
            }
            
        });
    });
});
    // =========================================================================
    function openOver(){
        $('#popupOverlay1s').fadeIn();
        document.getElementById("popupOverlay1s").style.display = "flex";
    }
    
    function closeOver(){
        $('#popupOverlay1s').fadeOut();
    };
    
    $(document).ready(function(){
        $(document).on('click', '.response', function() {
            var $form = $(this).closest('form');
            var userId = $form.find('input[name="userId"]').val();
            var Id = $form.find('input[name="Id"]').val();
            var savId = $form.find('input[name="savId"]').val();
            var amount = $form.find('input[name="amount"]').val();
            var email = $form.find('input[name="email"]').val();
            var response = $(this).data('action');
            var reason = $('#rejectionReason1').val(); 
            document.getElementById("loadingOverlay").style.display = "flex";

            var data = {
                userId: userId,
                Id: Id,
                response: response,
                amount: amount,
                email: email,
                savId: savId,
                reason: reason 
            };
    
            $.ajax({
                type: 'POST',
                url: '../withdrawReq.php',
                data: data,
                success: function(response) {
                    $('#loadingOverlay').hide();
                    setTimeout(function() {  
                    alert(response);
                        window.location.href = "dashboard.php";
                    }, 1000);
                }
            });
        });
    });
// =====================================================================

var modal = document.getElementById("loanModal");
var btn = document.getElementById("loanBtn");

var depmodal = document.getElementById("depModal");
var btndep = document.getElementById("depositer");
var spandep = document.getElementById("closedep");

var savmodal = document.getElementById("savModal");
var savbtn = document.getElementById("wdBtn");
var savspan = document.getElementById("closesav");

var span = document.getElementById("close");
btn.onclick = function() {
    modal.style.display = "block";
}
span.onclick = function() {
    modal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

btndep.onclick = function() {
    depmodal.style.display = "block";
}
spandep.onclick = function() {
    depmodal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == depmodal) {
        depmodal.style.display = "none";
    }
}
savbtn.onclick = function() {
    savmodal.style.display = "block";
}
savspan.onclick = function() {
    savmodal.style.display = "none";
}
window.onclick = function(event) {
    if (event.target == savmodal) {
        depmodal.style.display = "none";
    }
}

$(document).ready(function(){
$('#reqBut').click(function() {
    var formData = new FormData($('#loanForm')[0]);
$.ajax({
    type: 'POST',
    url: '../requestLoan.php',
    data: formData, 
    processData: false,
        contentType: false,
    success: function(response){
       alert(response); 
    //    $('#registerStatus').html(response);
    }
});
});
});
// =========================================================================
// function calculateTotal() {
//     const checkboxes = document.querySelectorAll('input[name="repayments[]"]:checked');
//     let totalAmount = 0;

//     checkboxes.forEach(checkbox => {
//         const totalDue = parseFloat(checkbox.dataset.totalDue);
//         totalAmount += totalDue;
//     });

//     document.getElementById('totalAmount').innerText = totalAmount.toFixed(2);
// }
// ========================================================================
function makePayment() {
    const formData = $('#repaymentForm').serialize();

    $.ajax({
        type: 'POST',
        url: '../processPayment.php',
        data: formData,
        success: function(response) {
            if (response.trim() === 'Payment Success') {
                alert('Payments successful');
                location.reload();
            } else {
                alert(response);
            }
        },
        error: function() {
            alert('An error occurred while processing your request.');
        }
    });
}
// ===========================================================================
function filterRepayments() {
    var selectedMonth = $('#monthFilter').val();
    var selectedStatus = $('input[name="statusFilter"]:checked').val();

    $.ajax({
        type: 'GET',
        url: 'includes/filteredBill.php',
        data: {
            month: selectedMonth,
            status: selectedStatus
        },
        success: function(response) {
            $('#repaymentsContainer').html(response);
        }
    });
}

$(document).ready(function() {
    $('#monthFilter, input[name="statusFilter"]').change(filterRepayments);
});


function calculateTotal() {
    var total = 0;
    $('input[name="repayments[]"]:checked').each(function() {
        total += parseFloat($(this).data('total-due'));
    });
    document.getElementById('totalAmount').innerText = total.toFixed(2);
    // Update total display if necessary
}
// =========================================================================
$(document).ready(function(){
    $('#withReq').click(function() {
        var formData = new FormData($('#withForm')[0]);
    $.ajax({
        type: 'POST',
        url: '../withdrawReq.php',
        data: formData, 
        processData: false,
            contentType: false,
        success: function(response){
           alert(response); 
        //    $('#registerStatus').html(response);
        }
    });
    });
    });
    // ==========================================================================
    $(document).ready(function(){
        $('#deposit').click(function() {
            var formData = new FormData($('#depoForm')[0]);
        $.ajax({
            type: 'POST',
            url: '../withdrawReq.php',
            data: formData, 
            processData: false,
                contentType: false,
            success: function(response){
               alert(response); 
            //    $('#registerStatus').html(response);
            }
        });
        });
        });
// ==============================================================================
function filterTransac() {
    var selectedFilter = $('input[name="filter"]:checked').val();
    var selectedSubFilter = $('input[name="subFilter"]:checked').val() || '';
    var selectedDateFilter = $('input[name="dateFilter"]:checked').val();
    var searchQuery = $('#searchBar').val();
    if (selectedFilter === 'loan') {
        $('#loanSubTypes').show();
        $('#savingsSubTypes').hide();
    } else if (selectedFilter === 'savings') {
        $('#loanSubTypes').hide(); 
        $('#savingsSubTypes').show(); 
    } else {
        $('#loanSubTypes').hide();
        $('#savingsSubTypes').hide();
        selectedSubFilter = ''; 
    }

    $.ajax({
        type: 'GET',
        url: 'includes/filterTransac.php',
        data: { 
            filter: selectedFilter,
            subFilter: selectedSubFilter,
            dateFilter: selectedDateFilter,
            search: searchQuery
        },
        success: function(response) {
            $('#transactionsTable').html(response);
        }
    });
}


// $(document).ready(function() {
//     filterTransac();
// });================================================================================================
function filterTransacUser() {
    var selectedFilter = $('input[name="filter"]:checked').val();
    var selectedSubFilter = $('input[name="subFilter"]:checked').val() || '';
    var selectedDateFilter = $('input[name="dateFilter"]:checked').val();

    if (selectedFilter === 'loan') {
        $('#loanSubTypes').show();
        $('#savingsSubTypes').hide();
    } else if (selectedFilter === 'savings') {
        $('#loanSubTypes').hide(); 
        $('#savingsSubTypes').show(); 
    } else {
        $('#loanSubTypes').hide();
        $('#savingsSubTypes').hide();
        selectedSubFilter = ''; 
    }

    $.ajax({
        type: 'GET',
        url: 'includes/userFilterTransac.php',
        data: { 
            filter: selectedFilter,
            subFilter: selectedSubFilter,
            dateFilter: selectedDateFilter

        },
        success: function(response) {
            $('#transactionsTable').html(response);
        }
    });
}
// =====================================================================================================

function updateAccountType(accountType,id) {
    // Send AJAX request to update account type
    $.ajax({
        type: 'POST',
        url: '../userAcctype.php', // Specify the PHP file to handle the request
        data: { account_type: accountType, id: id },
        success: function(response) {
            // Handle success response (if needed)
            alert(response);
        },
        error: function(xhr, status, error) {
            // Handle error (if needed)
            console.error(xhr.responseText);
        }
    });
}


