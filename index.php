
<!-- SOURCE: http://www.kodingmadesimple.com/2016/01/php-login-and-registration-script-with-mysql-example.html -->

<?php
session_start();

include_once 'db.php';
//set validation error flag as false
$error = false;

//check if form is submitted
if (isset($_POST['signup'])) {
    $fullname = mysqli_real_escape_string($connect, $_POST['fullname']);
    $companyname = mysqli_real_escape_string($connect, $_POST['companyname']);
    $email = mysqli_real_escape_string($connect,$_POST['email']);
    $loginname = preg_replace('/\s/', '', $companyname . '_' . $fullname);

    function randomWachtwoord() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
          $wachtwoord = array(); //remember to declare $pass as an array
          $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
          for ($i = 0; $i < 8; $i++) {
              $n = rand(0, $alphaLength);
              $wachtwoord[] = $alphabet[$n];
          }
          return implode($wachtwoord); //turn the array into a string
      }

      $p = randomWachtwoord($wachtwoord);

    //name can contain only alpha characters and space
    if (!preg_match("/^[a-zA-Z ]+$/",$fullname)) {
        $error = true;
        $fullname_error = "Name must contain only alphabets and space";
    }
    if (!preg_match("/^[a-zA-Z ]+$/",$companyname)) {
        $error = true;
        $companyname_error = "Name must contain only alphabets and space";
    }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $email_error = "Please Enter Valid Email ID";
    }

    if (!$error) {
        if(mysqli_query($connect, "INSERT INTO `userTable` (`fullname`, `companyname`, `email`, `loginname`, `wachtw`) VALUES ('".$fullname."','".$companyname."','".$email."','".$loginname."','".$p."')")) {

        


        $to      = $email;
        $subject = 'Emultech login settings - The flow calculator';
        $message = 'Dear ' . $fullname . ', ' . "\r\n\r\n" . 'Thank you for your interest in the flow calculator of Emultech. Hereby you have new login settings. If any questions remain, please reply to this email.' . "\r\n\r\n" . 'username: ' . $loginname . "\r\n" . 'password: ' . $p
        . "\r\n\r\n" . 'kind regards,  ' . "\r\n\r\n" . 'The Emultech team ';
        $headers = 'From: olivier.van.duuren@emultech.nl' . "\r\n" .
            'Reply-To: olivier.van.duuren@emultech.nl' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);


        header("Location: index.php");



         }
        else {  
        echo "There is already someone of your company registered with that specific name. Please try again.";
        }
  
    }
  }

//check if form is submitted
if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $pass = mysqli_real_escape_string($connect, $_POST['pass']);
    $result = mysqli_query($connect, "SELECT * FROM userTable WHERE loginname = '" . $username. "' and wachtw = '" . $pass . "'");

    if ($row = mysqli_fetch_array($result)) {
        $_SESSION['usr_id'] = $row['id'];
        $_SESSION['fullname'] = $row['fullname'];

    } else {
        $errormsg = "Incorrect user id or password!!!";
    }
}

//check if form is submitted
// if (isset($_POST['save'])) {

//     $query = "SELECT * FROM savedCalculations WHERE `id` = '" .$_SESSION['usr_id']. "' ";
// $results = mysqli_query($connect, $query);


// while ($row = mysqli_fetch_array($results)) {
   
//   echo $_POST["viscosityDP"];
// }
// }


?>

<!-- READ THIS
In the below document the flowcalculator is developed. Notice that changes must be executed with modesty. 
Many things are not needed to be changed. 

Definition list:
<div> = a group
id = a unique mark to apply layout to which can be addressed with a # sign in style.css
class = a general mark to apply layout to which can be addressed with a . sign in style.css
<tr> = a row in a table
<td> = a column in a table
<script> usually all magic happens here

 -->

<!DOCTYPE html>
<html>

<head> <!-- These are standard settings which probably will not be changed anymore --> 
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Emultech | The Flow Calculator</title> <!-- Tab title -->

  <link rel="stylesheet" href="style.css"> <!-- reference to style.css (layout) document  -->
  <link rel="icon" href="Emultech_icon_kleur.jpg"> <!-- Tab icon -->
  <script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.9.0.min.js"></script> <!-- Reference to a software library -->
</head>


<body>

<!-- Navigation bar in the top -->
<div id="navbar" >
  <ul>
    <li><a href="index.php"><img id="logo" src="Emultech_logo_monochroom_diapositief_large.png"></a></li>
    <li><img id="icon" src="Emultech_icon_monochroom_diapositief_large.png" ></li>
    <li><h1>The Flow Calculator</h1> <?php if(isset($_POST['login']) or isset($_POST['save']))
    { ?> <p id="fullnameuser"> <?php echo 'welcome' . ' ' . $_SESSION['fullname'];} else if (isset($_POST['logout'])) { echo 'Not logged in';}?> </p></li>
    <li><a href="savedcalculations.php" ><div id="scbutton" style="display:block;"onclick="ShowCalculations();"><?php echo 'saved calculations'?></div></a></li> <!-- This one only appears, when specific calculations are saved and has an eventlistener to a function elaborated on in the bottom-->
  </ul>
</div>


<?php 
  

if (isset($_SESSION['usr_id'])) { ?>
    
    



<!-- All the content on the website which concern input parameters, actually everything on first landing page after login -->
<div id="inputcontent" style="display: block;">

  <!-- Optional Parameters -->  
  <h3 style="display:none;">Optional Parameters</h3>
    <table class="opt_parametertable" style="display:none;">
      <tr> 
        <td><p>Concentration polymer DP:</p></td> <!-- Parameter name -->
        <td><input id="ConcentrationPol" type="number" value="" placeholder="mol/m&sup3"></td><!-- Parameter input field -->
        <td><p id="moreinfo" title="Explanation about the concentration of your polymer.">i</p></td><!-- Parameter explanation -->
      </tr> 
      <tr>
        <td><p>Concentration drug in DP:</p></td>
        <td><input type="number" id="ConcentrationDrug" value="" placeholder="mol/m&sup3"></td>
        <td><p id="moreinfo" title="Explanation about the concentration of your drug.">i</p></td>
      </tr>
      <tr>
        <td><p>Density solvent in DP:</p></td>
        <td><input type="number" id="Densitysolvent" value="" placeholder="kg/m&sup3"></td>
        <td><p id="moreinfo" title="Explanation about the density in DP of your solvent.">i</p></td>
      </tr>
    </table>

  

  <!-- Required Parameters -->
  <h3>Required Parameters</h3>
  
    <table class="logintable" name="Form">
      <form role="form" action="savedcalculations.php" method="post" name="signupform" onsubmit=" return javascript:void()">
      <!-- Preset applications and reset button -->
      <div class="apl-btns">
      <a href="#0"><div class="aplbutton" onclick="Application1();">application 1</div></a>
      <a href="#0"><div class="aplbutton" onclick="Application2();">application 2</div></a>
      <a href="#0"><div class="rstbutton" onclick="reset()">reset</div></a>
      </div>
      <tr>
        <td><p>Viscosity DP [Pa&#8226;s]:</p></td><!-- Parameter name -->
        <td><input type="number" id="ViscosityDP" value="" placeholder="Pascal * s" onchange="CheckVViscosityDP()" name="viscosityDP" required="required" step="0.0000001">*</td><!-- Parameter input field -->
        <td><p id="moreinfo" title="Explanation about the application viscosity in disphersed phase.">i</p></td><!-- Parameter explanation -->
        <td id="ViscosityDP_CHECK" style="display:none;">Realistic!</td><!-- Parameter feedback --> 
      </tr>
      <tr>
        <td><p>Density DP [kg/m&sup3]:</p></td>
        <td><input type="number" id="DensityDP" value="" placeholder="kg/m&sup3" onchange="CheckVDensityDP()" name="densityDP" required="required" step="0.0000001">*</td>
        <td><p id="moreinfo" title="Explanation about the application density in disphersed phase.">i</p></td>
        <td id="DensityDP_CHECK" style="display:none;">Realistic!</td>
      </tr>
      <tr>
        <td><p>Viscosity CP [Pa&#8226;s]:</p></td>
        <td><input type="number" id="ViscosityCP" value="" placeholder="Pascal * s" onchange="CheckVViscosityCP()" name="viscosityCP" required="required" step="0.0000001">*</td>
        <td><p id="moreinfo" title="Explanation about the application viscosity in the continuous phase.">i</p></td>
        <td id="ViscosityCP_CHECK" style="display:none;">Realistic!</td>      
      </tr>
      <tr>
        <td><p>Density CP [kg/m&sup3]:</p></td>
        <td><input type="number" id="DensityCP" value="" placeholder="kg/m&sup3" onchange="CheckVDensityCP()" name="densityCP" required="required" step="0.0000001">*</td>
        <td><p id="moreinfo" title="Explanation about the application density in the continuous phase.">i</p></td>
        <td id="DensityCP_CHECK" style="display:none;">Realistic!</td>
      </tr>
      <tr>
        <td><p>Interfacial tension [N/m]:</p></td>
        <td><input type="number" id="InterfacialTension" value="" placeholder="N/m" onchange="CheckVInterfacialTension()" name="interfacialtension" required="required" step="0.0000001">*</td>
        <td><p id="moreinfo" title="Explanation about the application interfacial tension.">i</p></td>
        <td id="InterfacialTension_CHECK" style="display:none;">Realistic!</td>
      </tr>
      
    </table>



  <!-- Select a disc -->
  <h3>Select a Disc</h3>
    <div class="apl-btns">
      <a href="#0"><div class="aplbutton" name="singlebutton" type="text" onclick="radiobtn1();">disc 78 x 50 &mu;</div></a>
      <a href="#0"><div class="aplbutton" name="singlebutton" type="text" onclick="radiobtn2();">disc 100 x 10 &mu;</div></a>
      <a href="#0"><div class="aplbutton" name="singlebutton" type="text" onclick="radiobtn3();">disc 2 x 100 &mu;</div></a>
      <a href="#0"><div class="aplbutton" name="singlebutton" type="text" onclick="radiobtn4();">disc 1 x 500 &mu;</div></a>
      </div>
    <table class="logintable" name="Form">
      <tr>
        <td><p>Amount of channels:</p></td><!-- Parameter name -->
        <td><input type="number" id="AmountOfChannels" value="" placeholder="1-100" onchange="CheckVAmountOfChannels()" pattern="/[^0-9\.]/g" name="amountofchannels" required="required" step="0.0000001">*</td><!-- Parameter input field -->
        <td><p id="moreinfo" title="Explanation about the amount of channels you might use.">i</p></td><!-- Parameter explanation -->
        <td id="AmountOfChannels_CHECK" style="display:none;">Realistic!</td><!-- Parameter feedback -->
      </tr>
      <tr>
        <td><p>Channel diameter [&mu;] :</p></td>
        <td><input type="number" id="ChannelDiameter" value="" placeholder="1-500 &mu;" onchange="CheckVChannelDiameter()" name="channeldiameter" required="required" step="0.0000001">*</td>
        <td><p id="moreinfo" title="Explanation about the channel you might use.">i</p></td>
        <td id="ChannelDiameter_CHECK" style="display:none;">Realistic!</td>
      </tr>

    </table>

  <!-- Name your application -->
  <h3>Name Your Application</h3>
    <table>
      <tr>
        <td><p>Application name:</p></td>
        <td><input type="text" id="CalculationName" value="" placeholder="please name your calculation" name="calculationname">*</td>
      </tr>

    </table>
    

</div> <!-- input content closed -->

<!-- Calculate button -->
<a href="javascript:void(0)" ><div id="calcbutton" onclick="FlowrateCP();FlowrateDP();CheckAnswer();CheckAllValues()" style="display:block;">Calculate</div></a>



<!-- Calculation loader -->
<div id="loader" style="display: none; margin: auto;">
  <div class="cssload-loader"></div>
</div>

<!-- All the content on the website which concern calculation output, actually everything on first landing page after calculation -->
<div id="outputcontent" onload="PageLoader()" style="display:block;">
  <h3 id="CalculationTitle" style="margin-top: 60px;"></h3> <!-- This name is filled in in the input parameter within input content -->
  
  <!-- Output table -->
  <table id="outputtable" >
    <tr>
      <td><p>Flowrate CP:</p></td>
       <td><p id="demo" step="0.0000001" name="demo"></p></td> <!-- This value is calculated in the function FlowrateCP() in the script below -->
       <td><input id="flowratecp" step="0.0000001" name="flowratecp" type="hidden"></td> <!-- This value is calculated in the function FlowrateCP() in the script below -->
    </tr>
    <tr>
      <td><p>Flowrate DP:</p></td>
      <td><p id="demo1" step="0.0000001" name="demo1"></p></td> <!-- This value is calculated in the function FlowrateDP() in the script below -->
       <td><input id="flowratedp" step="0.0000001" name="flowratedp" type="hidden"></td> <!-- This value is calculated in the function FlowrateCP() in the script below -->
    </tr>
  </table>

  <!-- Output explanation -->
  <p id="resultexpl" style="margin-bottom: 10px; width: 300px;"></p><!-- This specific text is calculated in the function CheckAllValues() in the script below -->
  <p id="CPtext"></p><!-- This specific text is calculated in the function CheckAllValues() in the script below -->

  <!-- Output buttons -->
  <a href="#" ><div id="AddCbutton" onclick="MoreCalculations()" style="display: inline;">Another Calculation</div></a>
  
<a href="javascript:void(0)" ><div id="AddCbutton" onclick="AddCbutton()" name="save" >Save Calculation 1 </div></a>

  <a href="javascript:void(0)" ><div onclick="AddCbutton()"><input type="submit" id="AddCbutton"  name="save" value="Save Calculation 2"/></div></a>



</form>
</div>

<div id="calculationlist" style="display:none;">
  <h3 style="margin-bottom: 40px;">Saved Calculations</h3>
  <div id="calculationbuttons"></div>
</div>

  <!-- Send button -->
<a href="#"><div id="mailbutton" onclick="MailForm()" style="display: none;">Send all calculations</div></a>


<p style="text-align: center; font-size: 12px;"><a href="logout.php" style="color: #10181f;" >Log Out</a></p>

<!-- All sort of noticable script which is responsible for the user interactions with the web application -->  
<script type="text/javascript">
  document.getElementById("scbutton").innerHTML = "Saved Calculations";

  var myVar;
  document.getElementById("loader").style.display = "none";
  document.getElementById("outputcontent").style.display = "none";

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

  function MailForm(txt) {
    d = document;
    var ALERT_TITLE = "Email form";
    var ALERT_TEXT =  "Please fill in your email address to receive your calculations:";
    var ALERT_BUTTON1_TEXT = "cancel";
    var ALERT_BUTTON2_TEXT = "Send mail";

    if(d.getElementById("modalContainer")) return;

    mObj = d.getElementsByTagName("body")[0].appendChild(d.createElement("div"));
    mObj.id = "modalContainer";
    mObj.style.height = d.documentElement.scrollHeight + "px";
    
    alertObj = mObj.appendChild(d.createElement("div"));
    alertObj.id = "alertBox";
    if(d.all && !window.opera) alertObj.style.top = document.documentElement.scrollTop + "px";
    alertObj.style.visiblity="visible";
    alertObj.setAttribute("style", "height: 300px; left: 355;")

    h3 = alertObj.appendChild(d.createElement("h3"));
    h3.appendChild(d.createTextNode(ALERT_TITLE));

    msg = alertObj.appendChild(d.createElement("p"));
    msg.appendChild(d.createTextNode(ALERT_TEXT));

    email = alertObj.appendChild(d.createElement("input"));
    email.appendChild(d.createTextNode(ALERT_TITLE));

    br = alertObj.appendChild(d.createElement("br"));
    

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
    btn2.onclick = function() { removeCustomAlert();return false; }

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
  function ful(){
  alert('Alert this pages');
  }

  function PageLoader() {
    myVar = setTimeout(showPage, 5000);
    document.getElementById("loader").style.display = "block";
    document.getElementById("calcbutton").style.display = "none";


  }

  function showPage() {
    document.getElementById("loader").style.display = "none";
    document.getElementById("outputcontent").style.display = "block";
    document.getElementById("calcbutton").style.display = "none";
    document.getElementById("mailbutton").style.display = "block";
    document.getElementById("calculationlist").style.display = "block";
  }

  //http://jsfiddle.net/hQKy9/
  function AddCbutton() {    
           var CalculationName = document.getElementById('CalculationName').value;  
           document.getElementById("CalculationName").innerHTML = CalculationName ;
           var ViscosityDP = document.getElementById('ViscosityDP').value;  
           var DensityDP = document.getElementById('DensityDP').value;  
           document.getElementById("DensityDP").innerHTML = DensityDP ; 
           document.getElementById("ViscosityDP").innerHTML = ViscosityDP ;
           var ViscosityCP = document.getElementById('ViscosityCP').value;  
           document.getElementById("ViscosityCP").innerHTML = ViscosityCP ;
           var DensityCP = document.getElementById('DensityCP').value;  
           document.getElementById("DensityCP").innerHTML = DensityCP ;
           var InterfacialTension = document.getElementById('InterfacialTension').value;  
           document.getElementById("InterfacialTension").innerHTML = InterfacialTension ;
           var AmountOfChannels = document.getElementById('AmountOfChannels').value;  
           document.getElementById("AmountOfChannels").innerHTML = AmountOfChannels ;
           var ChannelDiameter = document.getElementById('ChannelDiameter').value;  
           document.getElementById("ChannelDiameter").innerHTML = ChannelDiameter ;
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

  function removeTotaldiv() {
    var elem = document.getElementById('totaldiv');
      elem.parentNode.removeChild(elem);
    return false;
  }



  function ShowSCbutton() {
    var savebutton = document.getElementById("scbutton");
    var ShowCalculations = document.getElementById("calculationlist");
    var mailbutton = document.getElementById("mailbutton");
    savebutton.style.display = "block";

    if (savebutton.style.display === "none") {

                ShowCalculations.style.display = "none";
                mailbutton.style.display = "none";
                //window.scrollTo(0,document.body.scrollHeight);

            } else {
                savebutton.style.display = "block";
                ShowCalculations.style.display = "none";
                mailbutton.style.display = "none";
                
            }
  }


  // function AddCalculation() {

  //           var calculationX = document.createElement('div');
             
  //           calculationX.className = 'calculationx';       
  //             //div.style.backgroundColor = "black";

  //        document.getElementsByTagName('body')[0].appendChild(div);    
  // }

  function ShowCalculations() {
    var savebutton = document.getElementById("scbutton");
    var outputcontent = document.getElementById("outputcontent");
    var inputcontent = document.getElementById("inputcontent");
    var calcbutton = document.getElementById("calcbutton");   
    var ShowCalculations = document.getElementById("calculationlist");
    var mailbutton = document.getElementById("mailbutton");
    // var ShowCDetails = document.getElementById("calculationdetails1");
    var ShowCbuttons = document.getElementsByClassName("calculationxbutton");

    var count = 0;
      // var display = document.getElementById("displayCount");
      document.getElementById("scbutton").onclick = function(){
        inputcontent.style.display = "none";
        calcbutton.style.display = "none";
        outputcontent.style.display = "none";
          count++;

      if (count%2 == 1) {
      document.getElementById("scbutton").innerHTML = "Saved Calculations";
      ShowCalculations.style.display = "none";
      calcbutton.style.display = "block";
      mailbutton.style.display = "none";
      // ShowCDetails.style.display = "none";
      outputcontent.style.display = "none";
      inputcontent.style.display = "block";
      savebutton.style.display = "block"
    }
    else if (count%2 == 0){
      document.getElementById("scbutton").innerHTML = "Other Calculations";
      ShowCalculations.style.display = "none";
      calcbutton.style.display = "none";
      mailbutton.style.display = "block";
      // ShowCDetails.style.display = "block";
      ShowCbuttons.style.display = "block";
      outputcontent.style.display = "none";
      inputcontent.style.display = "none";

    }
    return count;
    }


    if (savebutton.style.display = "block") {
            document.getElementById("scbutton").innerHTML = "Other Calculation";
                outputcontent.style.display = "none";
                inputcontent.style.display = "none";
                ShowCalculations.style.display = "block";
                calcbutton.style.display = "none";
                mailbutton.style.display = "block";
            } else {
                outputcontent.style.display = "none";
                inputcontent.style.display = "block";
                ShowCalculations.style.display = "block";
                calcbutton.style.display = "block";
                mailbutton.style.display = "none";
            }
    if (ShowCalculations.style.display = "block") { 
      inputcontent.style.display = "none";
      calcbutton.style.display = "none";
    }
    

  }

  function HideCdetails() {
    var cdbutton = document.getElementById("calculation1");
    var calculationdetails1 = document.getElementById("calculationdetails1");

    var ViscosityDP = document.getElementById('viscosityDP').value;
      var ViscosityCP = document.getElementById('viscosityCP').value;
      var DensityDP = document.getElementById('densityDP').value;
      var DensityCP = document.getElementById('densityCP').value;
      var InterfacialTension = document.getElementById('interfacialtension').value;
      var AmountOfChannels = document.getElementById('amountofchannels').value;
      var ChannelDiameter = document.getElementById('channeldiameter').value;

      document.getElementById("ViscosityDP2").innerHTML = "";
      document.getElementById("ViscosityCP2").innerHTML = "";
      document.getElementById("DensityDP2").innerHTML = "";
      document.getElementById("DensityCP2").innerHTML = "";
      document.getElementById("InterfacialTension2").innerHTML = "";
      document.getElementById("AmountOfChannels2").innerHTML = "";
      document.getElementById("ChannelDiameter2").innerHTML = "";

      if (calculationdetails1.style.display = "block") {
               calculationdetails1.style.display = "none";
                
            } else {
                calculationdetails1.style.display = "none";
            }
  }

  function MoreCalculations() {
    var ShowCalculations = document.getElementById("calculationlist");
    var savebutton = document.getElementById("scbutton");
    var outputcontent = document.getElementById("outputcontent"); 
    var inputcontent = document.getElementById("inputcontent");
    savebutton.style.display = "none"
    
    if (savebutton.style.display = "block") {
                outputcontent.style.display = "none";
                ShowCalculations.style.display = "block";
                inputcontent.style.display = "block";
                calcbutton.style.display = "block";
                savebutton.style.display = "none"
      }

  }
  function CheckAllValues() {
    var ViscosityDP = document.getElementById('ViscosityDP').value;
      var ViscosityCP = document.getElementById('ViscosityCP').value;
      var DensityDP = document.getElementById('DensityDP').value;
      var DensityCP = document.getElementById('DensityCP').value;
      var InterfacialTension = document.getElementById('InterfacialTension').value;
      var AmountOfChannels = document.getElementById('AmountOfChannels').value;
      var ChannelDiameter = document.getElementById('ChannelDiameter').value;

      var count1 = 0;
      var count2 = 0;
      var count3 = 0;
      var count4 = 0;
      var count5 = 0;
      var count6 = 0;
      var count7 = 0;

      if ((ViscosityDP > 1) || (ViscosityDP < 0)) {
          count1 = 1;

            } 
    if ((DensityDP > 1500) || (DensityDP < 500)) {
            count2 = 1;
          } 
    if ((ViscosityCP > 1) || (ViscosityCP < 0)) {
          count3 = 1;
            }       
    if ((DensityCP > 1500) || (DensityCP < 500)) {
          count4 = 1;
            } 
    if ((InterfacialTension > 1) || (InterfacialTension < 0)) {
        count5 = 1;
          } 
    if ((AmountOfChannels > 100) || (AmountOfChannels < 0)) {
          count6 = 1; 
            }     
    if ((ChannelDiameter > 500) || (ChannelDiameter < 0)) {
          count7 = 1; 
            } 

    if (count1 + count2 + count3 + count4 + count5 + count6 + count7 == 0) {
      document.getElementById("resultexpl").innerHTML = "We believe your application is ready for our technology! If uncertain or in doubt please consult Emultech to prevent damage on the equipment.";
      
    }
    else if (count1 + count2 + count3 + count4 + count5 + count6 + count7 > 2)  {
      document.getElementById("resultexpl").innerHTML = "It seems your application is not ready for our technology! Please try other more realistic parameter values. If uncertain or in doubt please consult Emultech to prevent damage on the equipment.";
      
    }    
    else {
      document.getElementById("resultexpl").innerHTML = "It seems your application is ready for our technology! Please note that these flowrate values are only suggestive. If uncertain or in doubt please consult Emultech to prevent damage on the equipment. ";
      
    } 
    

  }
  function CheckValue() {
    var ViscosityDP = document.getElementById('ViscosityDP').value;
    var ViscosityDPred = document.getElementById('ViscosityDP');
      var ViscosityCP = document.getElementById('ViscosityCP').value;
      var DensityDP = document.getElementById('DensityDP').value;
      var DensityCP = document.getElementById('DensityCP').value;
      var InterfacialTension = document.getElementById('InterfacialTension').value;
      var AmountOfChannels = document.getElementById('AmountOfChannels').value;
      var ChannelDiameter = document.getElementById('ChannelDiameter').value;
      var ViscosityDP_CHECK = document.getElementById('ViscosityDP_CHECK').value;
      var resultexpl = document.getElementById("resultexpl");

    var count = 0;


    if ((ViscosityDP > 1) || (ViscosityDP <= 0)) {
          alert("Not realistic! Please fill in a value between 0 and 1.");

            } 
    if ((DensityDP > 1500) || (DensityDP < 500)) {
        alert("Not realistic! Please fill in a value between 500 and 1500.");

          } 
    if ((ViscosityCP > 1) || (ViscosityCP <= 0)) {
          alert("Not realistic! Please fill in a value between 0 and 1.");

            }       
    if ((DensityCP > 1500) || (DensityCP < 500)) {
          alert("Not realistic! Please fill in a value between 500 and 1500.");

            } 
    if ((InterfacialTension > 1) || (InterfacialTension <= 0)) {
        alert("Not realistic! Please fill in a value between 0 and 1.");

          } 
    if ((AmountOfChannels > 100) || (AmountOfChannels <= 0)) {
          alert("Not realistic! Please fill in a value between 0 and 100.");

            }     
    if ((ChannelDiameter > 500) || (ChannelDiameter <= 0)) {
          alert("Not realistic! Please fill in a value between 0 and 500.");

            } 
    
  }
  function CheckVViscosityDP() {
    var ViscosityDP = document.getElementById('ViscosityDP').value;
    var ViscosityDPred = document.getElementById('ViscosityDP');

    if ((ViscosityDP > 1) || (ViscosityDP <= 0)) {
          alert("Not realistic! Please fill in a value between 0 and 1.");
            ViscosityDPred.style.borderColor = "orange";
            } else {
              ViscosityDPred.style.borderColor = "#7ddbd3";
            }
  }
  function CheckVDensityDP() {
    var DensityDP = document.getElementById('DensityDP').value;
    var DensityDPred = document.getElementById('DensityDP');

      if ((DensityDP > 1500) || (DensityDP < 500)) {
        alert("Not realistic! Please fill in a value between 500 and 1500.");
            DensityDPred.style.borderColor = "orange";
          } else {
              DensityDPred.style.borderColor = "#7ddbd3";
            }
  }

  function CheckVViscosityCP() {
    var ViscosityCP = document.getElementById('ViscosityCP').value;
    var ViscosityCPred = document.getElementById('ViscosityCP');


      if ((ViscosityCP > 1) || (ViscosityCP <= 0)) {
        alert("Not realistic! Please fill in a value between 0 and 1.");
            ViscosityCPred.style.borderColor = "orange";
          }  else {
              ViscosityCPred.style.borderColor = "#7ddbd3";
            }
  }

  function CheckVDensityCP() {
    var DensityCP = document.getElementById('DensityCP').value;
    var DensityCPred = document.getElementById('DensityCP');


      if ((DensityCP > 1500) || (DensityCP < 500)) {
        alert("Not realistic! Please fill in a value between 500 and 1500.");
            DensityCPred.style.borderColor = "orange";
          } else {
              DensityCPred.style.borderColor = "#7ddbd3";
            }
  }

  function CheckVInterfacialTension() {
    var InterfacialTension = document.getElementById('InterfacialTension').value;
    var InterfacialTensionred = document.getElementById('InterfacialTension');


      if ((InterfacialTension > 1) || (InterfacialTension <= 0)) {
        alert("Not realistic! Please fill in a value between 0 and 1.");
            InterfacialTensionred.style.borderColor = "orange";
          } else {
              InterfacialTensionred.style.borderColor = "#7ddbd3";
            }
  }

  function CheckVAmountOfChannels() {
    var AmountOfChannels = document.getElementById('AmountOfChannels').value;
    var AmountOfChannelsred = document.getElementById('AmountOfChannels');


      if ((AmountOfChannels > 100) || (AmountOfChannels <= 0)) {
          alert("Not realistic! Please fill in a value between 0 and 100.");
              AmountOfChannelsred.style.borderColor = "orange";
            }  else {
              AmountOfChannelsred.style.borderColor = "#7ddbd3";
            }
  }

  function CheckVChannelDiameter() {
    var ChannelDiameter = document.getElementById('ChannelDiameter').value;
    var ChannelDiameterred = document.getElementById('ChannelDiameter');


      if ((ChannelDiameter > 500) || (ChannelDiameter <= 0)) {
          alert("Not realistic! Please fill in a value between 0 and 500.");
              ChannelDiameterred.style.borderColor = "orange";
            } else {
              ChannelDiameterred.style.borderColor = "#7ddbd3";
            }
    
  }
  function CheckAnswer() {

            var outputcontent = document.getElementById("outputcontent");
            var inputcontent = document.getElementById("inputcontent");
            var ViscosityDP = document.getElementById('ViscosityDP').value;
            var ViscosityDPred = document.getElementById('ViscosityDP');
            var ViscosityCP = document.getElementById('ViscosityCP').value;
            var ViscosityCPred = document.getElementById('ViscosityCP');
            var DensityDP = document.getElementById('DensityDP').value;
            var DensityDPred = document.getElementById('DensityDP');
            var DensityCP = document.getElementById('DensityCP').value;
            var DensityCPred = document.getElementById('DensityCP');
            var InterfacialTension = document.getElementById('InterfacialTension').value;
            var InterfacialTensionred = document.getElementById('InterfacialTension');
            var AmountOfChannels = document.getElementById('AmountOfChannels').value;
            var AmountOfChannelsred = document.getElementById('AmountOfChannels');
            var ChannelDiameter = document.getElementById('ChannelDiameter').value;
            var ChannelDiameterred = document.getElementById('ChannelDiameter');
            var CalculationName = document.getElementById('CalculationName').value;
            var CalculationNamered = document.getElementById('CalculationName');
            

            if( ViscosityDP == '' || ViscosityDP == null || ViscosityDP == 0 || ViscosityCP == '' || ViscosityCP == null || ViscosityCP == 0 || DensityDP == '' || DensityDP == null || DensityDP == 0 || DensityCP == '' || DensityCP == null || DensityCP == 0 || InterfacialTension == '' || InterfacialTension == null || InterfacialTension == 0 || AmountOfChannels == '' || AmountOfChannels == null || AmountOfChannels == 0 || ChannelDiameter == '' || ChannelDiameter == null || ChannelDiameter == 0 || CalculationName == '' || CalculationName == null){
              alert('Please fill in all required fields marked with * and/or exclude 0-values.');
             if (ViscosityDP == '' || ViscosityDP == null || ViscosityDP == 0) {
                ViscosityDPred.style.borderColor = "red";
              } else {
              ViscosityDPred.style.borderColor = "#7ddbd3";
            }

              if (DensityCP == '' || DensityCP == null || DensityCP == 0) {
                DensityCPred.style.borderColor = "red";
              } else {
              DensityCPred.style.borderColor = "#7ddbd3";
            }
             if (ViscosityCP == '' || ViscosityCP == null || ViscosityCP == 0) {
                ViscosityCPred.style.borderColor = "red";
              } else {
              ViscosityCPred.style.borderColor = "#7ddbd3";
            }
              if (DensityDP == '' || DensityDP == null || DensityDP == 0) {
                DensityDPred.style.borderColor = "red";
              } else {
              DensityDPred.style.borderColor = "#7ddbd3";
            }
              if (InterfacialTension == '' || InterfacialTension == null || InterfacialTension == 0) {
                InterfacialTensionred.style.borderColor = "red";
              } else {
              InterfacialTensionred.style.borderColor = "#7ddbd3";
            }
              if (AmountOfChannels == '' || AmountOfChannels == null || AmountOfChannels == 0) {
                AmountOfChannelsred.style.borderColor = "red";
              } else {
              AmountOfChannelsred.style.borderColor = "#7ddbd3";
            }
              if (ChannelDiameter == '' || ChannelDiameter == null || ChannelDiameter == 0) {
                ChannelDiameterred.style.borderColor = "red";
              } else {
              ChannelDiameterred.style.borderColor = "#7ddbd3";
            }
              if (CalculationName == '' || CalculationName == null || CalculationName == 0 ) {
                CalculationNamered.style.borderColor = "red";
              } else {
              CalculationNamered.style.borderColor = "#7ddbd3";
            }
              return false;
            } 
            else {
              PageLoader();
            }


            document.getElementById("CalculationTitle").innerHTML = "The results of your '" + CalculationName  + "'-calculation";
      }

  function ShowresultExpl() {
    
  }
      
  function  FlowrateCP(z) {
        var Cbutton = document.getElementById("calculationxbutton");
        var B11 = document.getElementById("ChannelDiameter").value;
          var B8 = document.getElementById("InterfacialTension").value;
          var B4 = document.getElementById("ViscosityDP").value;
          var B10 = document.getElementById("AmountOfChannels").value;
          var pow2 = Math.pow(10, 2);
          var pow6 = Math.pow(10, 6);
          var pow9 = Math.pow(10, 9);
          
          var x = 2*(B11/2/pow6);
          var y = Math.pow(x, 2);
          var z = (0.1*y*B8/B4*3.6*pow9)*B10;
          document.getElementById("demo").innerHTML = (z.toFixed(5) + " ml/hour");
          document.getElementById("flowratecp").value = z.toFixed(5);

          // document.getElementById("FlowrateCP2").innerHTML = (z.toFixed(5) + " ml/hour");
        
        if (z > 138) {
          document.getElementById("CPtext").innerHTML = "Due to the high continuous flow, production on the Emultech D will result in to short production. Please consult Emultech for alternative flowrates or alternative syringes.";
        } else if (48 < z < 138) {
          document.getElementById("CPtext").innerHTML = "Since your flowrate CP is " + z.toFixed(2) + " ml/hour we recommend the 25 ml syringe for a minimum production time of 10 minutes. Good luck with your application!";
        } else if (18 < z < 48) {
          document.getElementById("CPtext").innerHTML = "Since your flowrate CP is " + z.toFixed(2) + " ml/hour we recommend the 10 ml syringe for a minimum production time of 10 minutes. Good luck with your application!";
        } else {
          document.getElementById("CPtext").innerHTML = "Since your flowrate CP is " + z.toFixed(2) + " ml/hour we recommend the 5 ml syringe for a minimum production time of 10 minutes. Good luck with your application!";
        }
         
      //uit excel:
      //=(0.1*y*B8/B4*0.5*0,00272/((B6*B4)/(sqrtdensity*B8*(B11/2/pow6)))*3,6*pow9)*B10
  }

  function  FlowrateDP() {
        var B11 = document.getElementById("ChannelDiameter").value;
          var B8 = document.getElementById("InterfacialTension").value;
          var B4 = document.getElementById("ViscosityDP").value;
          var B10 = document.getElementById("AmountOfChannels").value;
          var B6 = document.getElementById("ViscosityCP").value;
          var B7 = document.getElementById("DensityCP").value;
          var B5 = document.getElementById("DensityDP").value;
          var pow2 = Math.pow(10, 2);
          var pow6 = Math.pow(10, 6);
          var pow9 = Math.pow(10, 9);
          var sqrtdensity = Math.sqrt(B7*B5);
          
          var x = 2*(B11/2/pow6);
          var y = Math.pow(x, 2);
          var z = (0.1*y*B8/B4*0.5*0.00272/((B6*B4)/(sqrtdensity*B8*(B11/2/pow6)))*3.6*pow9)*B10;
          document.getElementById("demo1").innerHTML= (z.toFixed(5) + " ml/hour");
          document.getElementById("flowratedp").value = z.toFixed(5);

          // document.getElementById("FlowrateDP2").innerHTML= (z + " ml/hour");
          
  }

  function reset() {
        var ViscosityDP = "" ; 
        document.getElementById("ViscosityDP").value = ViscosityDP;
        var DensityDP = "";
        document.getElementById("DensityDP").value = DensityDP;
        var ViscosityCP = "";
        document.getElementById("ViscosityCP").value = ViscosityCP;
        var DensityCP = "";
        document.getElementById("DensityCP").value = DensityCP;
        var InterfacialTension = "";
        document.getElementById("InterfacialTension").value = InterfacialTension;
      }

  function Application1() {
        var ViscosityDP = 0.00478 ; 
        document.getElementById("ViscosityDP").value = ViscosityDP;
        var DensityDP = 1202.5;
        document.getElementById("DensityDP").value = DensityDP;
        var ViscosityCP = 0.00127;
        document.getElementById("ViscosityCP").value = ViscosityCP;
        var DensityCP = 998.2;
        document.getElementById("DensityCP").value = DensityCP;
        var InterfacialTension = 0.0057;
        document.getElementById("InterfacialTension").value = InterfacialTension;
      }

  function Application2() {
        var ViscosityDP = 0.00315 ; 
        document.getElementById("ViscosityDP").value = ViscosityDP;
        var DensityDP = 1300;
        document.getElementById("DensityDP").value = DensityDP;
        var ViscosityCP = 0.00150;
        document.getElementById("ViscosityCP").value = ViscosityCP;
        var DensityCP = 1000;
        document.getElementById("DensityCP").value = DensityCP;
        var InterfacialTension = 0.007;
        document.getElementById("InterfacialTension").value = InterfacialTension;
      }

  function radiobtn1() {
        var AmountOfChannels1 = 78;
        document.getElementById("AmountOfChannels").value = AmountOfChannels1;
        var ChannelDiameter1 = 50;
        document.getElementById("ChannelDiameter").value = ChannelDiameter1;
      }

  function radiobtn2() {
        var AmountOfChannels2 = 100;
        document.getElementById("AmountOfChannels").value = AmountOfChannels2;
        var ChannelDiameter2 = 10;
        document.getElementById("ChannelDiameter").value = ChannelDiameter2;
      }

  function radiobtn3() {
        var AmountOfChannels3 = 2;
        document.getElementById("AmountOfChannels").value = AmountOfChannels3;
        var ChannelDiameter3 = 100;
        document.getElementById("ChannelDiameter").value = ChannelDiameter3;
      }

  function radiobtn4() {
        var AmountOfChannels4 = 1;
        document.getElementById("AmountOfChannels").value = AmountOfChannels4;
        var ChannelDiameter4 = 500;
        document.getElementById("ChannelDiameter").value = ChannelDiameter4;
      }
</script>

<!-- All sort of security script which is responsible for the user logging and safety of usage -->  
<script type="text/javascript">
  function InactivityTimeout(idle_time, callback) {
    this.state = 0; // 0-new, 1=active, 2=idle
    this.idle_time = idle_time;
    this.callback = callback;
    this.start();
  }

  InactivityTimeout.prototype.start = function() {
   this.state = 1;
   this.timer = setTimeout(this.timeout.bind(this), this.idle_time); 
  }
                            
  InactivityTimeout.prototype.activity = function() {
   if (this.state == 1) {
     clearTimeout(this.timer);
   }
   this.start();
  }

                            
  InactivityTimeout.prototype.timeout = function() {
     this.state = 2;
     this.callback();
  }

  /// usage

  var timer=new InactivityTimeout(600000, function() {
      alert("Time out!");
      window.location.href="logout.php";  
  });



  // function idleTimer() {
  //     var t;
  //     //window.onload = resetTimer;
  //     window.onmousemove = resetTimer; // catches mouse movements
  //     window.onmousedown = resetTimer; // catches mouse movements
  //     window.onclick = logout;     // catches mouse clicks
  //     window.onscroll = resetTimer;    // catches scrolling
  //     window.onkeypress = resetTimer;  //catches keyboard actions

  //     function logout() {
  //      alert("Timeout!");  
  //         window.location.href = 'file:///C:/Users/s145866/Documents/Emultech/logout.php';  //Adapt to actual logout script
  //     }

  //    function reload() {
  //          alert("reloaded");  
  //           window.location = self.location.href;  //Reloads the current page
  //    }

  //    function resetTimer() {
  //         clearTimeout(t);
  //         t = setTimeout(logout, 50000);  // time is in milliseconds (1000 is 1 second)
  //         t= setTimeout(reload, 50000);  // time is in milliseconds (1000 is 1 second)
  //     }
  // }
  // idleTimer();
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


<script type="text/javascript">
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

    
            
    

<script src="js/jquery-1.10.2.js"></script>
<script src="js/bootstrap.min.js"></script>



</div>
<?php } ?>

</body>
</html>
