<!DOCTYPE html>
<html lang="en" style="margin: 0px;">
<head>
    <style>
        @font-face {
          font-family: 'Roboto';
          font-style: normal;
          font-weight: normal;
          src: url("{{base_path}}/template/Roboto-Medium.ttf") format('truetype');
        }
        body {
          font-family: 'Roboto';
        }
      </style>
</head>
<body style="size: A4;margin: 0;max-width: 100%;font-family: 'Roboto', sans-serif; color:#1C1C1C;">
    <div class="wrapper" style="max-width: 100%;max-height: 100%;position: relative;overflow: hidden;">
        <img src="{{base_path}}/template/bg.jpg" alt="" class="bg-img" style="max-width: 100%;height: 100%;">
        <div class="qr-container" style="position: absolute;max-width: 220px;max-height: 220px;top: 320px;left: 50%; padding: 12px; transform: translate(-50%);">
            <img src="{{qr_code}}" alt="" id="qr-code" style="max-width: 100%;max-height: 100%;">
        </div>
        <div class="student_info" style="position: absolute;top: 550px;left: 50%;text-align: center;transform: translate(-50%);">
            <h2 style="font-size: 36px;font-weight: medium;margin-bottom: 10px;">{{student_name}}</h2>
            <h5 style="font-size: 20px;font-weight: medium;margin-top: 10px;">(Visitor Registration No: {{reg_no}})</h5>
        </div>
        
        <h5 class="link" style="font-size: 18px;font-weight: medium;margin-top: 10px;position: absolute;top: 66%;left: 50%;text-align: center; transform: translate(-50%);">Will Your Guardians Accompany You: <a href="{{pdf_link}}" style="text-decoration: underline;">{{pdf_link}}</a></h5>
    </div>    
</body>
</html>