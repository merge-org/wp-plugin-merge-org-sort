<?php
declare(strict_types=1);

namespace MergeOrg\Sort\Hooks;

use MergeOrg\Sort\Constants;
use MergeOrg\Sort\Service\ApiService;

final class AdminPageHook {

	/**
	 * @var ApiService
	 */
	private ApiService $apiService;

	/**
	 * @param ApiService $apiService
	 */
	public function __construct(ApiService $apiService) {
		$this->apiService = $apiService;
	}

	/**
	 * @return void
	 */
	public function __invoke(): void {
		add_menu_page(
			"Sort",
			"Sort",
			"manage_options",
			Constants::ADMIN_MAIN_PAGE_MENU_SLUG,
			$this->configurationHtmlRenderer(),
			"dashicons-products",
			100,
		);
	}

	/**
	 * @return callable
	 */
	public function configurationHtmlRenderer(): callable {
		return function() {
			if($options = ($_POST[Constants::OPTIONS_FIELD] ?? FALSE)) {
				$this->apiService->updateOptions($options);
			}

			$optionsName = Constants::OPTIONS_FIELD;
			$optionsDebugField = Constants::OPTIONS_DEBUG_FIELD;
			$debugInputName = "{$optionsName}[$optionsDebugField]";
			$debug = $this->apiService->getOptionDebug();
			$debugSelected = $debug ? "selected" : "";

			$optionUseLineItemQuantityField = Constants::OPTIONS_USE_LINE_ITEM_QUANTITY_FIELD;
			$useLineItemQuantityInputName = "{$optionsName}[$optionUseLineItemQuantityField]";
			$useLineItemQuantity = $this->apiService->getOptionUseLineItemQuantity();
			$useLineItemQuantitySelected = $useLineItemQuantity ? "selected" : "";

			echo "
				<div class='wrap'>
					<h1 class='wp-heading-inline'>SORT 📊</h1>
					<h3 class='wp-heading-inline'><pre>Sales Order Ranking Tool</pre></h3>
					<hr class='wp-header-end'>
					<form method='post' novalidate='novalidate'>
						<table class='form-table' role='presentation'>
							<tr>
								<th scope='row'><label for='$debugInputName'>Debug</label></th>
								<td>
									<select name='$debugInputName' id='$debugInputName'>
										<option value='no'>No</option>
										<option value='yes' $debugSelected>Yes</option>
									</select>
								</td>
							</tr>
							<tr>
								<th scope='row'><label for='$useLineItemQuantityInputName'>Use Order Item Quantity</label></th>
								<td>
									<select name='$useLineItemQuantityInputName' id='$useLineItemQuantityInputName'>
										<option value='no'>No</option>
										<option value='yes' $useLineItemQuantitySelected>Yes</option>
									</select>
								</td>
							</tr>
						</table>
						<p class='submit'><input type='submit' name='submit' id='submit' class='button button-primary' value='Αποθήκευση αλλαγών'></p>
					</form>
				</div>
				
			";
		};
	}
}
