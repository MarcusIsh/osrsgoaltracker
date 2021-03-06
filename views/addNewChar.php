<?php
use \Firebase\JWT\JWT;

require '../vendor/autoload.php';

if (!defined(__DIR__))
    define(__DIR__, dirname(__FILE__));
require_once(__DIR__ . '/../config/setup.php');
//print_r($_SESSION);
?>


<script>
    function addChar() {


                var data = {method: "addNew", "data": [{rsn: $('#rsn').val(), accountType: $('#account_type').val(), userID: $('#userID').val()}]};

                var url = '/addNewChar';
//
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: JSON.stringify(data),
                    contentType: "application/json",
                    async: false,
                    success: function (data) {
                        var response = $.parseJSON(data);
                        console.log(response);
                        if (response.status == "success") {
//                            $('#sendButton').css('display', 'none');
//                            $('#status').html("<h5 class=\"alert alert-success\"> SUCCESS </h5>");
                                alert("Success!");
                        } else {
                            $('#status').html("<h5 class=\"alert alert-danger\"> ERROR </h5>");
                        }
                    },
                    error: function () {
                        alert("There Was An Error Adding Link!");

                    }
                });
    }
</script>
<center><h3>Add Character to track!</h3></center>

<form id="formApplication" class="register-form mt-lg-12">
    <div class="row">
        <div class="col-sm-6">
            <label for="rsn">Runescape Name</label>
            <div class="form-group">
                <input type="text" id="rsn" name="rsn" class="form-control mb-sm" placeholder="*Runescape Name" required="required" />
            </div>
        </div>
        <div class="col-sm-6"></div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <label for="last_name">Account Type</label>
            <div class="form-group">
                <select id="account_type">
                    <option value="normal">normal</option>
                    <option value="ironMan">ironMan</option>
                    <option value="HCIM">HCIM</option>
                    <option value="UIM">UIM</option>
                </select>
            </div>
        </div>
        <div class="col-sm-6"></div>
        <input type="hidden" id="userID" name="userID" value="<?php echo $_SESSION['userid']; ?>" />
    </div>    
</form>


<!-- this is where buttons will go -->
<div class='row'>
    <center>
        <div id='sendContainer' class="col-sm-6">
            <button id="sendButton"  type="button" class="btn btn-primary" onclick='addChar();'>Submit Inquiry</button>
            <div id="status"></div>
        </div>
    </center>
</div>
<!-- This is where iframe maps will go -->
<script>

</script>