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
        <div class='col-sm-2'>
            <div>
                <h1>
                    <?php print $stats['rsn']; ?> 
                </h1>
            </div>
            <div>
                <h3>
                   Total: <?php print $stats['Overall']['level']; ?> 
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
<div class="row">
    <div class='col-md-4'>
        <div class='col-sm-2'>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Attack_icon.png'> &nbsp; <?php print $stats['attack'] ?> </div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Strength_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Defence_icon.png'></div>        
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Ranged_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Prayer_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Magic_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Runecrafting_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Construction_icon.png'></div>
        </div>
        <div class='col-sm-2'>
            <div class='col-sm-2 skill-col2'><img src='../assets/img/rsicons/Hitpoints_icon.png'></div>
            <div class='col-sm-2 skill-col2'><img src='../assets/img/rsicons/Agility_icon.png'></div>
            <div class='col-sm-2 skill-col2'><img src='../assets/img/rsicons/Herblore_icon.png'></div>
            <div class='col-sm-2 skill-col2'><img src='../assets/img/rsicons/Thieving_icon.png'></div>
            <div class='col-sm-2 skill-col2'><img src='../assets/img/rsicons/Crafting_icon.png'></div>
            <div class='col-sm-2 skill-col2'><img src='../assets/img/rsicons/Fletching_icon.png'></div>
            <div class='col-sm-2 skill-col2'><img src='../assets/img/rsicons/Slayer_icon.png'></div>
            <div class='col-sm-2 skill-col2'><img src='../assets/img/rsicons/Hunter_icon.png'></div>
        </div>
        <div class='col-sm-2'>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Mining_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Smithing_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Fishing_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Cooking_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Firemaking_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Woodcutting_icon.png'></div>
            <div class='col-sm-2 skill'><img src='../assets/img/rsicons/Farming_icon.png'></div>
        </div>
    </div>
</div>
   





<script>

</script>