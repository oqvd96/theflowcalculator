<?php 

session_start();

include_once 'db.php';

if (isset($_POST['save'])) {
  $id = $_SESSION['usr_id'];
      // $loginname = mysqli_query($connect, "SELECT loginname FROM userTable WHERE fullname = '" . $_SESSION['fullname'] . "'");
  $calculationname = mysqli_real_escape_string($connect, $_POST['calculationname']);
  $viscosityDP = mysqli_real_escape_string($connect, $_POST['viscosityDP']);
  $densityDP = mysqli_real_escape_string($connect, $_POST['densityDP']);
  $viscosityCP = mysqli_real_escape_string($connect, $_POST['viscosityCP']);
  $densityCP = mysqli_real_escape_string($connect, $_POST['densityCP']);
  $interfacialtension = mysqli_real_escape_string($connect, $_POST['interfacialtension']);
  $channeldiameter = mysqli_real_escape_string($connect, $_POST['channeldiameter']);
  $amountofchannels = mysqli_real_escape_string($connect, $_POST['amountofchannels']);
  $flowrateCP = mysqli_real_escape_string($connect, $_POST['flowratecp']);
  $flowrateDP = mysqli_real_escape_string($connect, $_POST['flowratedp']);



  if (mysqli_query($connect, "INSERT INTO `savedCalculations` (`id`, `calculationname`, `viscosityDP`, `densityDP`, `viscosityCP`, `densityCP`, `interfacialtension`, `channeldiameter`, `amountofchannels` , `flowrateCP`, `flowrateDP`) VALUES ('".$id."','".$calculationname."','".$viscosityDP."','".$densityDP."','".$viscosityCP."','".$densityCP."','".$interfacialtension."','".$channeldiameter."','".$amountofchannels."','".$flowrateCP."','".$flowrateDP."')")) {
    echo "Successfully saved!"; 
   }
  else {  
  echo "Failed to connect to MySQL: " . mysqli_connect_error();



  }

  }

  if (isset($_POST['mail'])) {
  $email = "SELECT `email` FROM `userTable` WHERE id = $session_id";
  $e = mysqli_query($connect, $email);

    if ($row = mysqli_fetch_array($e)) {
        $to      = "olivier.van.duuren@emultech.nl";
        $subject = 'Your calculations - The flow calculator';
        $message = 'Dear ' . $fullname . ', ' . "\r\n\r\n" . 'Thank you for your using the flow calculator of Emultech. Hereby you have your saved calculations. If any questions remain, please reply to this email.' . "\r\n\r\n" . "Calculations:" . "\r\n\r\n" . 'kind regards,  ' . "\r\n\r\n" . 'The Emultech team ';
        $headers = 'From: olivier.van.duuren@emultech.nl' . "\r\n" .
            'Reply-To: olivier.van.duuren@emultech.nl' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);

        header('Location: index.php');
    } else {
        echo "Your email is not successfully sent, please try this action again another time.";
    }
  
        




  } 

if (isset($_SESSION['usr_id'])) { ?>

<!DOCTYPE html>
<html>

<head> <!-- These are standard settings which probably will not be changed anymore --> 
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Emultech | The Flow Calculator</title> <!-- Tab title -->

  <link rel="stylesheet" href="style.css"> <!-- reference to style.css (layout) document  -->
  <link rel="icon" href="Emultech_icon_kleur.jpg"> <!-- Tab icon -->
  <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.9.0.min.js"></script> <!-- Reference to a software library -->
  <script>
function clickCounter() {
    if(typeof(Storage) !== "undefined") {
        if (localStorage.clickcount) {
            localStorage.clickcount = Number(localStorage.clickcount)+1;
        } else {
            localStorage.clickcount = 1;
            localStorage.clear();
        }
        document.getElementById("result").innerHTML = "You have clicked the button " + localStorage.clickcount + " time(s).";
    } else {
        document.getElementById("result").innerHTML = "Sorry, your browser does not support web storage...";
    }
}

$(window).unload(function(){
  localStorage.myPageDataArr=undefined;
});
</script>
</head>


<body>

<!-- Navigation bar in the top -->
<div id="navbar" >
  <ul>
    <li><a href="index.php"><img id="logo" src="Emultech_logo_monochroom_diapositief_large.png"></a></li>
    <li><img id="icon" src="Emultech_icon_monochroom_diapositief_large.png" ></li>
    <li><h1>The Flow Calculator</h1> </li>
    <li><a href="index.php" ><div id="scbutton" style="display: block;"onclick="ShowCalculations();"><?php echo 'other calculations'?></div></a></li> <!-- This one only appears, when specific calculations are saved and has an eventlistener to a function elaborated on in the bottom-->
  </ul>
</div>


<!-- All the content on the website which concern calculation details when a saved calculation button is hit -->
<div id="calculationlist" style="margin-top: 100px;">
  <h3 style="margin-top: 100px;">Saved Calculations</h3>
  <li id="totaldiv">

  <br>

  <?php 

  $session_id = $_SESSION['usr_id'];

  // $current_calcID = "SELECT `calcID` FROM `savedCalculations` WHERE id = $session_id ORDER BY timeSaved DESC LIMIT 1";
  // $q = mysqli_query($connect, $current_calcID);
  // while ($row = mysqli_fetch_assoc($q)) {
  //  $current = $row['calcID'];  
  // }
  $x = 2;
  $date = date("d");
  $var = "SELECT `calculationname`, `viscosityDP`, `densityDP`, `viscosityCP`, `densityCP`, `interfacialtension`, `amountofchannels`, `channeldiameter`, `flowrateCP`, `flowrateDP` FROM `savedCalculations` WHERE id = $session_id ORDER BY timeSaved DESC LIMIT $x";


  $query = mysqli_query($connect, $var);
  
if(mysqli_num_rows($query) > 0)

    while ($row = mysqli_fetch_assoc($query)) {
      echo "<li id='totaldiv' style='margin: 50px;'>";
      echo "<table>";
      echo "<div class='calculationxbutton'>";
      echo $row['calculationname'];
      echo "</div>";
      echo "<br/>";
      echo "<tr>";
      echo "<td>";
      echo "Viscosity DP: ";
      echo "</td>";
      echo "<td>";
      echo $row['viscosityDP'];
      echo "</td>";
      echo "<td>";
      echo "Pa&#8226;s";
      echo "</td>";
      echo "</tr>";
      

      echo "<tr>";
      echo "<td>";
      echo "Density DP: ";
      echo "</td>";
      echo "<td>";
      echo $row['densityDP'];
      echo "</td>";
      echo "<td>";
      echo "kg/m&sup3";
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>";
      echo "Viscosity CP: ";
      echo "</td>";
      echo "<td>";
      echo $row['viscosityCP'];
      echo "</td>";
      echo "<td>";
      echo "Pa&#8226;s";
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>";
      echo "Density CP: ";
      echo "</td>";
      echo "<td>";
      echo $row['densityCP'];
      echo "</td>";
      echo "<td>";
      echo "kg/m&sup3";
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>";
      echo "Interfacial tension: ";
      echo "</td>";
      echo "<td>";
      echo $row['interfacialtension'];
      echo "</td>";
      echo "<td>";
      echo "N/m";
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>";
      echo "Amount of Channels: ";
      echo "</td>";
      echo "<td>";
      echo $row['amountofchannels'];
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>";
      echo "Channel Diameter: ";
      echo "</td>";
      echo "<td>";
      echo $row['channeldiameter'];
      echo "</td>";
      echo "<td>";
      echo "&mu;";
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>";
      echo "Flowrate CP: ";
      echo "</td>";
      echo "<td>";
      echo $row['flowrateCP'];
      echo "</td>";
      echo "<td>";
      echo "ml/h";
      echo "</td>";
      echo "</tr>";

      echo "<tr>";
      echo "<td>";
      echo "Flowrate DP: ";
      echo "</td>";
      echo "<td>";
      echo $row['flowrateDP'];
      echo "</td>";
      echo "<td>";
      echo "ml/h";
      echo "</td>";
      echo "</tr>";
      echo "</table>";

      echo "<div id='remove' onclick='DeleteForm()'>";
      echo "X";
      echo "</div>";

      echo "</li>";



     
      
 
  }
else
    echo "You have no calculations yet. <br />";

?> 
<a href="#"><div id="mailbutton" onclick="MailForm()" style="display: block;">Send all calculations</div></a> <?php

?>

<p><button onclick="clickCounter()" type="button">Click me!</button></p>
<div id="result"></div>
  


</div>
</li>


 <p style="text-align: center; font-size: 12px;"><a href="logout.php" style="color: #10181f;" >Log Out</a></p>

<script type="text/javascript">
function MailForm(txt) {
    d = document;
    var ALERT_TITLE = "Email form";
    var ALERT_TEXT =  "Please fill in your email address to receive your calculations:";
    var ALERT_BUTTON1_TEXT = "cancel";
    var ALERT_BUTTON2_TEXT = "Send mail";
    // var CALC_LIST = "<?php 

    // $var = 'SELECT `calculationname` FROM `savedCalculations` WHERE id = $session_id ORDER BY timeSaved DESC LIMIT 1';

    // $session_id = $_SESSION['usr_id'];
    // $query = mysqli_query($connect, $var);
  
    // while ($row = mysqli_fetch_assoc($query)) {
    //   echo $row['calculationname'];
    //   echo '<br/>';
    // }
    // ?>";

    if(d.getElementById("modalContainer")) return;

    mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
    mObj.id = "modalContainer";
    mObj.style.height = d.documentElement.scrollHeight + "px";
    
    alertObj = mObj.appendChild(d.createElement("div"));
    alertObj.id = "alertBox";
    if(d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
    alertObj.style.visiblity="visible";
    alertObj.setAttribute("style", "height: 300px;")

    h3 = alertObj.appendChild(d.createElement("h3"));
    h3.appendChild(d.createTextNode(ALERT_TITLE));

    msg = alertObj.appendChild(d.createElement("p"));
    msg.appendChild(d.createTextNode(ALERT_TEXT));

    // msg = alertObj.appendChild(d.createElement("p"));
    // msg.appendChild(d.createTextNode(CALC_LIST));
    

    email = alertObj.appendChild(d.createElement("input"));
    email.appendChild(d.createTextNode(ALERT_TITLE));

    br = alertObj.appendChild(d.createElement("br"));
    

    btn1 = alertObj.appendChild(d.createElement("a"));
    btn1.id = "sendBtn";
    btn1.appendChild(d.createTextNode(ALERT_BUTTON1_TEXT));
    btn1.href = "#";
    btn1.focus();
    btn1.onclick = function() { removeCustomAlert();return false; }

    mailform = alertObj.appendChild(d.createElement("form"));
    mailform.method = "post";


    btn2 = mailform.appendChild(d.createElement("input"));
    alertObj.appendChild(btn2);
    btn2.type = "submit";
    btn2.id = "sendBtn";
    btn2.name = "mail";
    btn2.appendChild(d.createTextNode(ALERT_BUTTON2_TEXT));
    btn2.href = "#";
    btn2.focus();
    btn2.onclick = function() { removeCustomAlert(); return true;}

    alertObj.style.display = "block";
  }

function DeleteForm(txt) {
    d = document;
    var ALERT_TITLE = "";
    var ALERT_TEXT =  "Your calculation will be deleted permanently, are you sure?";
    var ALERT_BUTTON1_TEXT = "no";
    var ALERT_BUTTON2_TEXT = "yes";

    if(d.getElementById("modalContainer")) return;

    mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
    mObj.id = "modalContainer";
    mObj.style.height = d.documentElement.scrollHeight + "px";
    
    alertObj = mObj.appendChild(d.createElement("div"));
    alertObj.id = "alertBox";
    if(d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
    alertObj.style.visiblity="visible";


    br = alertObj.appendChild(d.createElement("br"));

    msg = alertObj.appendChild(d.createElement("p"));
    msg.appendChild(d.createTextNode(ALERT_TEXT));

    btn1 = alertObj.appendChild(d.createElement("a"));
    btn1.id = "sendBtn";
    btn1.appendChild(d.createTextNode(ALERT_BUTTON1_TEXT));
    btn1.href = "#";
    btn1.focus();
    btn1.onclick = function() { removeCustomAlert();return false; }

    btn2 = alertObj.appendChild(d.createElement("a"));
    btn2.id = "sendBtn";
    btn2.appendChild(d.createTextNode(ALERT_BUTTON2_TEXT));
    btn2.href = "#";
    btn2.focus();
    btn2.onclick = function() { removeCustomAlert();removeTotaldiv(); return false; }

    

    alertObj.style.display = "block";
  }

  function removeCustomAlert() {
    document.getElementsByTagName("body")[0].removeChild(document.getElementById("modalContainer"));
  }

  function removeTotaldiv() {
    var elem = document.getElementById('totaldiv');
      elem.parentNode.removeChild(elem);
    return false;
  }


function AddCbutton() {   

           var CalculationName = document.getElementById('CalculationName').innerHTML;  
           // document.getElementById("CalculationName").innerHTML = CalculationName ;
           // var ViscosityDP = document.getElementById('ViscosityDP2').value;  
           // var DensityDP = document.getElementById('DensityDP2').value;  
           // document.getElementById("DensityDP2").innerHTML = DensityDP ; 
           // document.getElementById("ViscosityDP2").innerHTML = ViscosityDP ;
           // var ViscosityCP = document.getElementById('ViscosityCP2').value;  
           // document.getElementById("viscosityCP").innerHTML = ViscosityCP ;
           // var DensityCP = document.getElementById('DensityCP2').value;  
           // document.getElementById("DensityCP2").innerHTML = DensityCP ;
           // var InterfacialTension = document.getElementById('InterfacialTension2').value;  
           // document.getElementById("InterfacialTension2").innerHTML = InterfacialTension ;
           // var AmountOfChannels = document.getElementById('AmountOfChannels2').value;  
           // document.getElementById("AmountOfChannels2").innerHTML = AmountOfChannels ;
           // var ChannelDiameter = document.getElementById('ChannelDiameter2').value;  
           // document.getElementById("ChannelDiameter2").innerHTML = ChannelDiameter ;
           // var FlowrateCP = document.getElementById('demo').value; 
           // document.getElementById("demo").innerHTML = FlowrateCP ;
           // var FlowrateDP = document.getElementById('demo1').value;  
           // document.getElementById("demo1").innerHTML = FlowrateDP ;
           var count = 0;

           var cdlist = document.getElementById("calculationlist");
       
           var totaldiv = document.createElement('li');
           totaldiv.id = "totaldiv";
           var objTo = document.getElementById('calculationbuttons')
           var div = document.createElement('div');
           
           totaldiv.appendChild(div);
           
           cdlist.appendChild(totaldiv);

           $(div).addClass("calculationxbutton");
           var aTag = document.createElement('a');
           var aTag2 = document.createElement('a');
           aTag.innerHTML = (CalculationName);
       div.appendChild(aTag);
       
       
      

       var table = document.createElement('table');
       var tr1 = document.createElement('tr');
       var tr2 = document.createElement('tr');
       var tr3 = document.createElement('tr');
       var tr4 = document.createElement('tr');
       var tr5 = document.createElement('tr');
       var tr6 = document.createElement('tr');
       var tr7 = document.createElement('tr');
       // var tr8 = document.createElement('tr');
       
       // var tr9 = document.createElement('tr');
       
       var td1 = document.createElement('td');
       var td2 = document.createElement('td');
       var td3 = document.createElement('td');
       var td4 = document.createElement('td');
       var td5 = document.createElement('td');
       var td6 = document.createElement('td');
       var td7 = document.createElement('td');
       // var td8 = document.createElement('td');
       // var td9 = document.createElement('td');
       var p1 = document.createElement('p');
       p1.setAttribute("style", "margin: 5px;" );
       var p2 = document.createElement('p');
       p2.setAttribute("style", "margin: 5px;" );
       var p3 = document.createElement('p');
       p3.setAttribute("style", "margin: 5px;" );
       var p4 = document.createElement('p');
       p4.setAttribute("style", "margin: 5px;" );
       var p5 = document.createElement('p');
       p5.setAttribute("style", "margin: 5px;" );
       var p6 = document.createElement('p');
       p6.setAttribute("style", "margin: 5px;" );
       var p7 = document.createElement('p');
       p7.setAttribute("style", "margin: 5px;" );
       // var p8 = document.createElement('p');
       // p8.id = "FlowrateCP2";
       // var p9 = document.createElement('p');
       // p9.id = "FlowrateDP2";

       p1.innerHTML = "Viscosity DP: " + " "+ ViscosityDP + " " + "Pa&#8226;s";
       p2.innerHTML = "Density DP: " + " "+ DensityDP + " " + "kg/m&sup3";
       p3.innerHTML = "Viscosity CP: " + " "+ ViscosityCP + " " + "Pa&#8226;s";
       p4.innerHTML = "Density DP: " + " "+ DensityDP + " " + "kg/m&sup3";
       p5.innerHTML = "Interfacial tension: " + " "+ InterfacialTension + " " + "N/m";
       p6.innerHTML = "Amount of channels: " + " "+ AmountOfChannels ;
       p7.innerHTML = "Channel diameter: " + " "+ ChannelDiameter + " " + "mu";
       // p8.innerHTML = "FlowrateCP: " + FlowrateCP;
       // p9.innerHTML = "FlowrateDP: " + FlowrateDP;

       td1.appendChild(p1);
       td2.appendChild(p2);
       td3.appendChild(p3);
       td4.appendChild(p4);
       td5.appendChild(p5);
       td6.appendChild(p6);
       td7.appendChild(p7);
       // td8.appendChild(p8);
       // td9.appendChild(p9);

       tr1.appendChild(td1);
       tr2.appendChild(td2);
       tr3.appendChild(td3);
       tr4.appendChild(td4);
       tr5.appendChild(td5);
       tr6.appendChild(td6);
       tr7.appendChild(td7);
       // tr8.appendChild(td8);
       // tr9.appendChild(td9);

       table.appendChild(tr1);
       table.appendChild(tr2);
       table.appendChild(tr3);
       table.appendChild(tr4);
       table.appendChild(tr6);
       table.appendChild(tr7);
       // table.appendChild(tr8);
       // table.appendChild(tr9);

       totaldiv.appendChild(table);

       var div2 = document.createElement('div');
           div2.id= "remove";
           div2.innerHTML = "X"
           div2.setAttribute("onclick", "DeleteForm();" );
       div2.appendChild(aTag2);
       totaldiv.appendChild(div2);
       count++;

       if (count > 0) {
        // alert("This calculation is already saved.");
        var AddCbutton = document.getElementById("AddCbutton");
        AddCbutton.style.display = "none"
        // AddCbutton.style.backgroundColor = "#777779"
        // AddCbutton.style.color = "white";
       } 
       

      
      
  }
  function CheckAllValues() {
    var fullname = document.getElementById('fullname');
    var fullnamered = document.getElementById('fullname');
    var companyname = document.getElementById('companyname');
    var companynamered = document.getElementById('companyname');
    var emailaddress = document.getElementById('emailaddress');
    var emailaddressred = document.getElementById('emailaddress');
    var filter = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    var filter2 = /^[a-zA-Z ]+$/ ;


    if( fullname == '' || fullname == null || companyname == '' || companyname == null || emailaddress == '' || emailaddress == null ){
              alert('Please fill in all required fields marked with * and/or exclude 0-values.');
             if (fullname == '' || fullname == null) {
                fullnamered.style.borderColor = "red";
              } else {
              fullnamered.style.borderColor = "#7ddbd3";
            }

              if (companyname == '' || companyname == null) {
                companynamered.style.borderColor = "red";
              } else {
              companynamered.style.borderColor = "#7ddbd3";
            }
             if (emailaddress == '' || emailaddress == null) {
                emailaddressred.style.borderColor = "red";
                
              } else {
              emailaddressred.style.borderColor = "#7ddbd3";
            }  
            return false;
            }  else if (!filter.test(emailaddress.value)) {
              alert('Please provide a valid email address');
            } else if (!filter2.test(fullname.value)) {
              alert('Please provide a valid full name');
            } else if (!filter2.test(companyname.value)) {
              alert('Please provide a valid full name');
            } else {
              var fullname2 = document.getElementById('fullname').value;
            var companyname2 = document.getElementById('companyname').value;
              var login = document.getElementById("login");
              var loginname = document.createElement('p');
              loginname.id = "loginname";
              var fullname3 = fullname2.replace(/\s/g, '');
              var companyname3 = companyname2.replace(/\s/g, '');
              loginname.innerHTML = companyname3 + "_" + fullname3;
              login.appendChild(loginname);
              var randomstring = Math.random().toString(36).slice(-8);
              var password = document.createElement('p');
              password.id = "password";
              password.innerHTML = randomstring;
              login.appendChild(password);
              // location="TheFlowCalculator.html" ;
            }

            
    

  }

</script>
<?php } else { ?>

<script type="text/javascript">
var ALERT_TITLE = " ";
var ALERT_BUTTON_TEXT = "Ok";

if(document.getElementById) {
  window.alert = function(txt) {
    createCustomAlert(txt);
  }
}

function createCustomAlert(txt) {
  d = document;

  if(d.getElementById("modalContainer")) return;

  mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
  mObj.id = "modalContainer";
  mObj.style.height = d.documentElement.scrollHeight + "px";
  
  alertObj = mObj.appendChild(d.createElement("div"));
  alertObj.id = "alertBox";
  if(d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
  alertObj.style.visiblity="visible";

  h3 = alertObj.appendChild(d.createElement("h3"));
  h3.appendChild(d.createTextNode(ALERT_TITLE));

  msg = alertObj.appendChild(d.createElement("p"));
  //msg.appendChild(d.createTextNode(txt));
  msg.innerHTML = txt;

  btn = alertObj.appendChild(d.createElement("a"));
  btn.id = "closeBtn";
  btn.appendChild(d.createTextNode(ALERT_BUTTON_TEXT));
  btn.href = "#";
  btn.focus();
  btn.onclick = function() { removeCustomAlert();return false; }

  alertObj.style.display = "block";
  
}

function removeCustomAlert() {
  document.getElementsByTagName("body")[0].removeChild(document.getElementById("modalContainer"));
}
function ful(){
alert('Alert this pages');
}
</script>

<!-- Navigation bar in the top -->
<div id="navbar" >
  <ul>
    <li><img id="logo" src="Emultech_logo_monochroom_diapositief_large.png"></li>
    <li><img id="icon" src="Emultech_icon_monochroom_diapositief_large.png" ></li>
    <li><h1>The Flow Calculator</h1></li>
    <li><a href="#" ><div id="scbutton" onclick="ShowCalculations();"></div></a></li> <!-- This one only appears, when specific calculations are saved and has an eventlistener to a function elaborated on in the bottom-->
  </ul>
</div>

<div id="loginpage">


    <div class="container">
    <div class="row">
      <table class="logintable">
        <div class="col-md-4 col-md-offset-4 well">
            <form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="signupform">
                
                
                    <tr><td><h3 style="margin-left: 5px;">Register here</h3></td></tr>
                    <div class="form-group">
                      <tr><td><input type="text" id="fullname" value="" placeholder="Full name" name='fullname' required value="<?php if($error) echo $fullname; ?>"></td></tr>
                      <span class="text-danger"><?php if (isset($fullname_error)) echo $fullname_error; ?></span>
                    </div>
                    <div class="form-group">
                      <tr><td><input type="text" id="companyname" value="" placeholder="Company name please" name='companyname' required value="<?php if($error) echo $companyname; ?>"></td></tr>
                      <span class="text-danger"><?php if (isset($company_error)) echo $company_error; ?></span>
                    </div>
                    <div class="form-group">
                      <tr><td><input type="text" id="emailaddress" value="" placeholder="Email address please" name='email' required value="<?php if($error) echo $emailaddress; ?>"></td></tr>
                      <span class="text-danger"><?php if (isset($email_error)) echo $fullname_error; ?></span>
                    </div>

                    <div class="form-group">
                        <tr><td><input type="submit" name="signup" value="Sign Up" id="registerbutton" /></td></tr>
                    </div>
              
            </form>
            <span class="text-success"><?php if (isset($successmsg)) { echo $successmsg; } ?></span>
            <span class="text-danger"><?php if (isset($errormsg)) { echo $errormsg; } ?></span>
        </div>
        </table>
    </div>
</div>

  <h3 style="margin-top: 100px; display: block;"></h3>
  <div class="container">
    <div class="row">
      <table class="logintable">
        <div class="col-md-4 col-md-offset-4 well">
            <form role="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="loginform">
                    <tr><td><h3 style="margin-left: 5px;">Login here</h3></td></tr>
                    <div class="form-group">
                      <tr><td><input type="text" id="username" value="Emultech_OliviervanDuuren" placeholder="Login name" name="username" required class="form-control"></td></tr>
                    </div>

                    <div class="form-group">
                      <tr><td><input type="password" id="pass" value="" placeholder="password" name="pass" required class="form-control"></td></tr>
                    </div>

                    <div class="form-group">
                        <tr><td><input type="submit" name="login" id="loginbutton" value="Login" /></td></tr>  
                    </div>
            </form>
            <span class="text-danger"><?php if (isset($errormsg)) { echo $errormsg; } ?></span>
        </div>
        </table>
    </div>
</div>
  



</div>




    
            
    

<script src="js/jquery-1.10.2.js"></script>
<script src="js/bootstrap.min.js"></script>



</div>
<?php } ?>

 </body>
 </html> 