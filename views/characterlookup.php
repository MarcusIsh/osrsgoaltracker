<?php
use \Firebase\JWT\JWT;

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

if (!defined(__DIR__))
    define(__DIR__, dirname(__FILE__));
require_once(__DIR__ . '/../config/setup.php');

print_r($stats);
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
<!--<center><h3></h3></center>-->
<div class='row character-header'>
    
        <?php
            if($stats['characterType'] == "IronMan"){
                print "<div id='characterType' class='col-sm-1'><img src='../assets/img/rsicons/Ironman_helm_detail.png' height='216.5px' width='114.5px'> </div>";
            } elseif ($stats['characterType'] == "UIM") {
                print "<div id='characterType' class='col-sm-1'><img src='../assets/img/rsicons/Ultimate_ironman_helm_detail.png' height='216.5px' width='114.5px'> </div>";
            } elseif ($stats['characterType'] == "HCIM") {
                print "<div id='characterType' class='col-sm-1'><img src='../assets/img/rsicons/Hardcore_ironman_helm_detail.png' height='216.5px' width='114.5px'> </div>";
            } else {
                print "<div id='characterType' class='col-sm-1'><img src='../assets/img/rsicons/Helm_of_neitiznot_detail.png' height='216.5px' width='114.5px'> </div>";
            }
        ?> 
        <div class='col-sm-1'>
            <div>
                <h3>
                    <?php print $stats['rsn']; ?> 
                </h3>
            </div>
            <div>
                <h3>
                   Total Level: <?php print $stats['Overall']['level']; ?> 
                </h3>
            </div>
        </div>
    
<!--    <div class='col-sm'>
        <div>
            <h3>
                <?php print $stats['rsn']; ?> 
            </h3>
        </div>
    </div>-->
</div>
   





<script>

</script>