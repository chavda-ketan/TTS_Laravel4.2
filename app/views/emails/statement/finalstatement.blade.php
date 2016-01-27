<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style>
/* -------------------------------------
        GLOBAL
------------------------------------- */
* {
    margin: 0;
    padding: 0;
    font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
    font-size: 80%;
    line-height: 1.4;
}

img {
    max-width: 100%;
}

body {
    -webkit-font-smoothing: antialiased;
    -webkit-text-size-adjust: none;
    width: 100%!important;
    height: 100%;
}


/* -------------------------------------
        ELEMENTS
------------------------------------- */
a {
    color: #348eda;
}

.btn-primary {
    text-decoration: none;
    color: #FFF;
    background-color: #348eda;
    border: solid #348eda;
    border-width: 10px 20px;
    line-height: 2;
    font-weight: bold;
    margin-right: 10px;
    text-align: center;
    cursor: pointer;
    display: inline-block;
    border-radius: 25px;
}

.btn-secondary {
    text-decoration: none;
    color: #FFF;
    background-color: #aaa;
    border: solid #aaa;
    border-width: 10px 20px;
    line-height: 2;
    font-weight: bold;
    margin-right: 10px;
    text-align: center;
    cursor: pointer;
    display: inline-block;
    border-radius: 25px;
}

.last {
    margin-bottom: 0;
}

.first {
    margin-top: 0;
}

.padding {
    padding: 10px 0;
}


/* -------------------------------------
        BODY
------------------------------------- */
table.body-wrap {
    width: 100%;
    padding: 0px;
}

table.body-wrap .container {
}

table.body-wrap .logo {
    border: none;
}


/* -------------------------------------
        FOOTER
------------------------------------- */
table.footer-wrap {
    width: 100%;
    clear: both!important;
}

.footer-wrap .container p {
    font-size: 12px;
    color: #666;

}

table.footer-wrap a {
    color: #999;
}


/* -------------------------------------
        TYPOGRAPHY
------------------------------------- */
h1, h2, h3 {
    font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
    color: #000;
    margin: 10px 0 10px;
    line-height: 1.2;
    font-weight: 200;
}

h1 {
    font-size: 180%;
}
h2 {
    font-size: 160%;
}
h3 {
    font-size: 140%;
}

p, ul, ol {
    margin-bottom: 10px;
    font-weight: normal;
    font-size: 14px;
}

ul li, ol li {
    margin-left: 5px;
    list-style-position: inside;
}

/* ---------------------------------------------------
        RESPONSIVENESS
        Nuke it from orbit. It's the only way to be sure.
------------------------------------------------------ */

/* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
.container {
    display: block!important;
    max-width: 1200px!important;
    margin: 0 auto!important; /* makes it centered */
    clear: both!important;
    min-width: 600px !important;
}

/* Set the padding on the td rather than the div for Outlook compatibility */
.body-wrap .container {
    padding: 20px;
}

/* This should also be a block element, so that it will fill 100% of the .container */
.content {
    max-width: 1200px;
    margin: 0 auto;
    display: block;
}

/* Let's make sure tables in the content area are 100% wide */
.content table {
    width: 100%;
}

.head {
    border-bottom: 1px dashed #ddd;
}

td {
    vertical-align: top;
}

.realtable {
    border-spacing: 0px;
    border-collapse: collapse;
    font-size: 13px;
}
.realtable, .realtable td, .realtable th {
    text-align: left;
    font-weight: normal;
    border: 1px solid #ccc;
    border-collapse: collapse;
    border-spacing: 0px;
}
.summarytable, .summarytable td {
    border: none !important;
}
.summarytable {
}

.headright {
    text-align: right !important;
    padding: 0 !important;
}

.realtable tbody tr td {
    border-top: none;
    border-bottom: none;
}
@media print {
    * {
        font-size: 12px;
    }
}
</style>
</head>

<body bgcolor="#f6f6f6">
<table class="body-wrap" bgcolor="#f6f6f6">
    <tr>
        <td></td>
        <td class="container" bgcolor="#FFFFFF">

            <!-- remittance -->
            <div class="content">
                <p style="font-size: 15px">
                    Please find attached your {{ $month }} end-of-month statement and invoices. This includes transactions completed at all our locations.<br/><br/>
                    Statement balances are due by the 15th of the following month.<br/><br/>
                    Please remit your payment in the amount of ${{ number_format((float) $balance, 2, '.', '') }} to:<br/><br/>
                    <b style="font-size: 110%">The TechKnow Space Inc.</b><br/>
                    33 City Centre Dr Unit #142<br/>
                    Mississauga, ON L5B 2N5<br/>
                </p>
            </div>
            <!-- /remittance -->

        </td>
        <td></td>
    </tr>
</table>
</body>
</html>