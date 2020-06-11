<?php
// phpcs:disable WordPress.NamingConventions.ValidFunctionName

defined( 'ABSPATH' ) or die();

/**
 * Contains the user info (from form submissions) for free product selection
 */
class WJECF_Free_Product_Form_Item {

	const MAX_QUANTITY   = true;
	const EMPTY_QUANTITY = false;

	private $product_id              = null;
	private $product_or_variation_id = null;

	private $quantity   = self::EMPTY_QUANTITY;
	private $attributes = array();

	/**
	 * The product_id belonging to the free product selection
	 * @param int $product_id
	 * @param array|int $data The product_id, or an array with values e.g. [ 'quantity' => $qty, 'attributes' => $attribs ]
	 * @return type
	 */
	public function __construct( $data = null ) {
		if ( is_numeric( $data ) ) {
			$this->setProductId( $data );
		} else {
			if ( ! isset( $data['product_id'] ) ) {
				throw new Exception( 'product_id is obligatory' );
			}

			$this->setProductId( $data['product_id'] );
			if ( isset( $data['quantity'] ) ) {
				$this->setQuantity( $data['quantity'] );
			}
			if ( isset( $data['attributes'] ) ) {
				$this->setAttributes( $data['attributes'] );
			}
		}
	}
	/**
	 * Amount selected by the customer
	 * @return int|bool
	 */
	public function getQuantity() {
		return $this->quantity;
	}

	/**
	 * Amount selected by the customer
	 * @param int|bool $value
	 */
	public function setQuantity( $value ) {
		if ( is_numeric( $value ) ) {
			$this->quantity = intval( $value );
		} elseif ( empty( $value ) ) {
			$this->quantity = self::EMPTY_QUANTITY;
		} else {
			$this->quantity = self::MAX_QUANTITY;
		}
	}

	/**
	 * Selected attributes for product variations
	 * @return array The attributes e.g. [ "attribute_pa_color" => "green", ... ]
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	public function hasAttributes() {
		foreach ( $this->getAttributes() as $key => $value ) {
			if ( ! empty( $value ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Selected attributes for product variations
	 * @param array $value The attributes e.g. [ "attribute_pa_color" => "green", ... ]
	 */
	public function setAttributes( $value ) {
		$product_or_variation_id = null;
		$this->attributes        = $value;
	}

	/**
	 * Gets the product. Yields null if the Product Id is invalid.
	 * @return WC_Product|null
	 */
	public function getProduct() {
		$product = wc_get_product( $this->product_id );
		return empty( $product ) ? null : $product;
	}

	/**
	 * Gets the ProductId
	 * @return int
	 */
	public function getProductId() {
		return $this->product_id;
	}

	private function setProductId( $value ) {
		$this->product_id = apply_filters( 'wjecf_get_product_id', intval( $value ), 10, 1 ); //WPML
	}

	/**
	 * Gets the product, or the variation (based on the selected attributes).
	 * Returns null if no valid variation can be found.
	 * @return WC_Product or null
	 */
	public function getProductOrVariation() {
		$product_or_variation_id = $this->getProductOrVariationId();
		if ( empty( $product_or_variation_id ) ) {
			return null;
		}

		$product_or_variation = wc_get_product( $product_or_variation_id );
		return empty( $product_or_variation ) ? null : $product_or_variation;
	}

	/**
	 * Gets the product or the variation id (based on the selected attributes).
	 * Returns null if no valid variation can be found.
	 * @return int|null
	 */
	public function getProductOrVariationId() {
		if ( ! isset( $this->product_or_variation_id ) ) {
			$product = $this->getProduct();
			if ( empty( $product ) ) {
				return null;
			}

			if ( $product->is_type( 'variable' ) ) {
				//Variable product
				$attributes                    = $this->getAttributes();
				$variation_id                  = WJECF_WC()->find_matching_product_variation( $product, $attributes );
				$this->product_or_variation_id = empty( $variation_id ) ? null : $variation_id;
			} else {
				//Normal product
				$this->product_or_variation_id = $this->getProductId();
			}
		}

		return $this->product_or_variation_id;
	}

	/**
	 * Convert to an array for session storage
	 * @return array
	 */
	public function toArray() {
		$retval               = array();
		$retval['product_id'] = $this->product_id;
		$retval['quantity']   = $this->quantity;
		if ( ! empty( $this->attributes ) ) {
			$retval['attributes'] = $this->attributes;
		}

		return $retval;
	}
}
