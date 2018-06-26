<?php
use \Firebase\JWT\JWT;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

if (!defined(__DIR__))
    define(__DIR__, dirname(__FILE__));
require_once(__DIR__ . '/../config/setup.php');

?>
<script>
    function getAllCharInfo() {


                var data = {method: "getAll", "data": [{}]};

                var url = '/characterlookup';
//
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: JSON.stringify(data),
                    contentType: "application/json",
                    async: false,
                    success: function (data) {
                        var response = $.parseJSON(data);
                        if (response.status == "success") {
                            $('#sendButton').css('display', 'none');
                            $('#status').html("<h5 class=\"alert alert-success\"> SUCCESS </h5>");
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
<div class="row">
    
</div>





<script>

</script>