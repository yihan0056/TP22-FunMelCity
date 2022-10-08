<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$isSCCFreeVersion = defined( 'STYLISH_COST_CALCULATOR_VERSION' );
?>
<style>
	#scc-editing-area-loading {
		height: 100%;
		width: 0;
		position: fixed;
		z-index: 1;
		top: 0;
		left: 0;
		cursor: wait;
		background-color: rgb(0, 0, 0);
		background-color: rgba(0, 0, 0, 0.9);
		overflow-x: hidden;
		transition: 0.5s;
	}

	#scc-editing-area-loading .center {
		position: absolute;
		height: auto;
		width: 50%;
		top: calc(50% - 20%);
		left: calc(50% - 20%);
		padding: 10px;
	}

	#scc-editing-area-loading .sk-chase {
		width: 40px;
		height: 40px;
		left: calc(65% - 20%);
		position: relative;
		animation: scc-sk-chase 2.5s infinite linear both;
	}

	#scc-editing-area-loading .sk-chase-dot {
		width: 100%;
		height: 100%;
		position: absolute;
		left: 0;
		top: 0;
		animation: scc-sk-chase-dot 2.0s infinite ease-in-out both;
	}

	#scc-editing-area-loading .sk-chase-dot:before {
		content: '';
		display: block;
		width: 25%;
		height: 25%;
		background-color: #fff;
		border-radius: 100%;
		animation: scc-sk-chase-dot-before 2.0s infinite ease-in-out both;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(1) {
		animation-delay: -1.1s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(2) {
		animation-delay: -1.0s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(3) {
		animation-delay: -0.9s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(4) {
		animation-delay: -0.8s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(5) {
		animation-delay: -0.7s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(6) {
		animation-delay: -0.6s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(1):before {
		animation-delay: -1.1s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(2):before {
		animation-delay: -1.0s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(3):before {
		animation-delay: -0.9s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(4):before {
		animation-delay: -0.8s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(5):before {
		animation-delay: -0.7s;
	}

	#scc-editing-area-loading .sk-chase-dot:nth-child(6):before {
		animation-delay: -0.6s;
	}

	@keyframes scc-sk-chase {
		100% {
			transform: rotate(360deg);
		}
	}

	@keyframes scc-sk-chase-dot {

		80%,
		100% {
			transform: rotate(360deg);
		}
	}

	@keyframes scc-sk-chase-dot-before {
		50% {
			transform: scale(0.4);
		}

		100%,
		0% {
			transform: scale(1.0);
		}
	}
</style>
<div id="scc-editing-area-loading" style="width:100%">
	<div class="center">
		<div class="sk-chase">
			<div class="sk-chase-dot"></div>
			<div class="sk-chase-dot"></div>
			<div class="sk-chase-dot"></div>
			<div class="sk-chase-dot"></div>
			<div class="sk-chase-dot"></div>
			<div class="sk-chase-dot"></div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function() {
		jQuery('#scc-editing-area-loading').css('display', 'none')
	})
</script>
<div class="container-fluid">
	<div class="row ms-0 mt-3 align-items-center">
		<div class="col-12 col-md-5 col-lg-5 ">
				<div class="scc-custom-version-info align-middle">
					<a href="https://stylishcostcalculator.com/" class="scc-header">
						<img src="
						<?php
						echo esc_url( SCC_URL . 'assets/images/scc-logo.png' );
						if ( ! defined( 'ABSPATH' ) ) {
							exit; // Exit if accessed directly
						}
						?>
									" class="img-responsive1" style="padding-bottom:20px;max-width: 160px" alt="Image">
					</a>
					<span class="scc_plug_ver">
						<?php
						// $opt = get_option('df_scclk_opt');
						if ( $isSCCFreeVersion ) {
							echo 'Free';
						} else {
							echo 'Premium';
						}
						?>
					</span>
				</div>
		</div>
		<div class="col-12 col-md-7 col-lg-7 scc-navbar">
				<div class="scc-top-nav-container">
					<ul class="scc-edit-nav-items">
						<li><a class="fw-bold" href="<?php echo esc_url( admin_url( 'admin.php?page=scc-tabs' ) ); ?>">Add New</a></li>
						<li><a class="fw-bold" href="<?php echo esc_url( admin_url( 'admin.php?page=Stylish_Cost_Calculator_EditItems' ) ); ?>">Edit Existing</a></li>
						<li class="dropdown">
							<a class="dropdown-toggle fw-bold" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Feedback <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a class="fw-bold" target="_blank" href="https://stylishcostcalculator.com/how-can-we-be-better/">Send Feedback</a></li>
								<li><a class="fw-bold" target="_blank" href="https://stylishcostcalculator.com/poll/new-features/">Suggest Feature</a></li>
							</ul>
						</li>

						<li class="dropdown">
							<a  class="dropdown-toggle fw-bold" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Support <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a class="fw-bold" target="_blank" href="https://designful.freshdesk.com/support/solutions/48000446985">User Guides</a></li>
								<li><a class="fw-bold" target="_blank" href="https://designful.freshdesk.com/support/solutions/folders/48000657938">Video Guides</a></li>
								<li><a class="fw-bold" target="_blank" href="<?php echo esc_url( admin_url( 'admin.php?page=stylish_cost_calculator_Diagnostic' ) ); ?>">Diagnostic</a></li>
								<li><a class="fw-bold" target="_blank" href="https://designful.freshdesk.com/support/solutions/folders/48000670797">Troubleshooting</a></li>
								<li><a class="fw-bold" target="_blank" href="https://stylishcostcalculator.com/support/">Contact Support</a></li>
								<li><a class="fw-bold" target="_blank" href="https://members.stylishcostcalculator.com/">Member's Portal</a></li>
							</ul>
						</li>
					</ul>
					<?php if (isset($_REQUEST['id_form'])) { ?>    
                        <button class="btn btn-warning">
                            <a class="fw-bold text-decoration-none text-dark" href="<?php echo admin_url() ?>">WP Dashboard</a>
                        </button>
                    <?php } ?>
				</div>
		</div>
	</div>
	<div id="debug_messages_wrapper" class="d-none"></div>
	<script>
		function showSettingsTab(type) {
			switch (type) {
				case "font":
					b_font.click()
					break
				case "translation":
					b_tans.click()
					break
				case "settings":
					b_calc.click()
					break
			}
		}

		/**
		 * *Handles download of backup
		 */

		function downloadBackup(isPremium) {
			return
		}
	</script>
