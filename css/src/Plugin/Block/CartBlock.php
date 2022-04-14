<?php

namespace Drupal\website_cart\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Block to hold the cart contents
 * 
 * @Block(
 *      id = "cart_block",
 *      admin_label = @Translation("Website Cart Block"),
 *      category = @Translation("Website Cart")
 * )
 */
class CartBlock extends BlockBase {
    /**
     * {@inheritDoc}
     */
    public function build()
    {
        return [
            '#markup' => 'content',
            '#attached' => [
                'library' => ['website_cart/website-cart']
            ],
        ];
    }

    public function getCacheMaxAge()
    {
        return 0;
    }

}