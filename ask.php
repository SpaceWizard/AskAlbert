<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Ask Albert - Ask</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Loading Bootstrap -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Loading Flat UI -->
    <link href="css/flat-ui.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/fixed.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-tagsinput.css">
    <link rel="shortcut icon" href="images/favicon.ico">
    <link href="bootstrap/css/prettify.css" rel="stylesheet">
    <link href="css/docs.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
        <!-- Top Navbar-->
     
         <nav class="navbar navbar-inverse navbar-static-top navbar-lg" role="navigation">
                  <div class="container">
                  <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-01">
                      <span class="sr-only">Toggle navigation</span>
                    </button>
                    <a class="navbar-brand" href="home.php">Ask Albert</a>
                  </div>
                  <div class="collapse navbar-collapse" id="navbar-collapse-01">
                    <!-- List of page links -->
                    <ul class="nav navbar-nav">           
                      <li><a href="discussion.php">Discussion</a></li>
                      <li class="active"><a href="ask.php">Ask A Question</a></li>
                      <li><a href="profile.php">Profile</a></li>
                    </ul>           
                    
                    <!-- Search Form-->
                    <form class="navbar-form navbar-left" action="#" role="search">
                      <div class="form-group">
                        <div class="input-group">
                          
                          <input class="form-control" id="navbarInput-01" type="search" placeholder="Search">
                          <span class="input-group-btn">
                            <button type="submit" class="btn"><span class="fui-search"></span></button>
                          </span>            
                        </div>
                      </div>               
                    </form>
                    
                    <p class="navbar-text navbar-right">Signed in as <a class="navbar-link" href="#">Anurag Soni</a></p>
                    
                  </div><!-- /.navbar-collapse -->
                  </div>
            </nav><!-- /navbar -->
       
        <div class="container">
            <div class="row">
              <div class="col-lg-8">
                  <form role="form">
                    <div class="form-group">
                      <input type="text" class="form-control flat" id="QuestionTitle" placeholder="Click here to Title Your Post">
                    </div>
                    <div class="form-group">
                      <textarea class="form-control" id="QuestionBody" rows="10" placeholder="Body goes here..."></textarea>
                    </div>
                    <div class="form-group">
                      <input type="text" id="QuestionTags" class="form-control" value="" data-role="tagsinput" placeholder="Enter tags separated by commas..." />
                    </div>
                    <button type="submit" class="btn btn-inverse">Submit</button>
                  </form>
              </div>
            </div>
        </div>

        <footer class="navbar-inverse navbar-fixed-bottom">
          <div class="container">
            <div class="row">
              <div class="col-lg-12">
                <h3 class="footer-title">Subscribe</h3>
                <p>
                  &copy; 2014 <a href="#">Ask Albert</a>
                </p>
              </div> <!-- /col-xs-7 -->

              
          </div>
        </footer>

    <!-- Load JS here for greater good =============================-->
    
    <script src="js/jquery-1.8.3.min.js"></script>
    <script src="js/bootstrap-tagsinput.js"></script>
    <script src="js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="js/jquery.ui.touch-punch.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-select.js"></script>
    <script src="js/bootstrap-switch.js"></script>
    <script src="js/flatui-checkbox.js"></script>
    <script src="js/flatui-radio.js"></script>
    <script src="js/jquery.tagsinput.js"></script>
    <script src="js/jquery.placeholder.js"></script>
  </body>
</html>