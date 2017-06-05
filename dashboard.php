<?php 
ini_set("display_errors", "1");
error_reporting(E_ALL);?>
<?php include 'header.php';?>
<script src="<?php echo $base_url;?>js/d3.v3.min.js"></script>
<script src="<?php echo $base_url;?>js/d3.tip.v0.6.3.js"></script>

  <!-- Content Wrapper. Contains page content -->
 <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  <div class="row">
       <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
       <div class="round_area">
       <div class="first_row">
       <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
       <div class="round_one">
       <h1 id="active"></h1>
       <h6>Active User</h6>
       </div>
       </div>
       <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
       <div class="round_two">
       <h1 id="courier"></h1>
       <h6>Courier Guy</h6>
       </div>
       </div>
       <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
       <div class="round_three">
       <h1 id="booking"></h1>
       <h6>Booking</h6>
       </div>
       </div>
       <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
       <div class="round_four">
       <h1 id="currentTrip"></h1>
       <h6>Current Trip</h6>
       </div>
       </div>
       </div>
       <div class="first_row">
       <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
       <div class="round_five">
       <h1 id="complain">0</h1>
       <h6>Complain</h6>
       </div>
       </div>
       <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
       <div class="round_six">
       <h1 id="promo"></h1>
       <h6>Active Promo Code</h6>
       </div>
       </div>
       <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
       <div class="round_seven">
       <h1 id="dispute"></h1>
       <h6>Dispute Case</h6>
       </div>
       </div>
       <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
       <div class="round_eight">
       <h1 id="payout"></h1>
       <h6>Payout Request</h6>
       </div>
       </div>
       </div>
       </div>
       </div>
       <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
       <div class="walet_area">
       <div class="first_walet">
       <div class="walet_icon">
       <img src="dist/img/icon/c_wallet.png" align="company_wallet"/>
       </div>
       <div class="company_text">
       <h5>Company wallet</h5>
       <h1 id ="cWallet">75,000.00</h1>
       </div>
       </div>
       <div class="first_walet">
       <div class="walet_icon">
       <img src="dist/img/icon/p_wallet.png" align="company_wallet"/>        
       </div>
       <div class="company_text">
       <h5>Promo wallet</h5>
       <h1 id="">32,000.00</h1>
       </div>
       </div>
       <div class="first_walet">
       <div class="walet_icon">
       <img src="dist/img/icon/s_wallet.png" align="company_wallet"/>       
       </div>
       <div class="company_text">
       <h5>Suspense wallet</h5>
       <h1 id="sWallet"></h1>
       </div>
       </div>
       </div>
       </div>
       <div class="first_row">
       <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
       <div class="left_graph">
       <h6> Last 1 week booking graph</h6>
       <div id="chart"></div>

       </div>
       </div>
       <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
       <div class="right_graph">
       <h6>Financial transaction</h6>
       <div id="frequencyChart"></div>
       </div>
      
       </div>
       </div>
</div>
  </div>

  <!-- /.content-wrapper -->
  <?php include 'footer.php';?>
