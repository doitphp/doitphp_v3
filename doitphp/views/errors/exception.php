<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Exception</title>
<style type="text/css">
body {font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;font-size:16px;font-weight:normal;color:#303133;background-color:#fff;}
h1 {font-weight:normal;font-size:24px;color:#da5430;}
h3 {margin-top:24px;font-weight:bold;font-size:18px;}
p {color: #2091cf;}
</style>
</head>

<body>
<h1>Exception</h1>
<h3>Description</h3>
<p><?php echo $message; ?></p>
<h3>Source File</h3>
<p><?php echo $sourceFile; ?></p>
<h3>Stack Trace</h3>
<div><pre><?php echo $traceString; ?></pre></div>
</body>
</html>