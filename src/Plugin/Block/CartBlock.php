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
        $request = \Drupal::request();
        $session = $request->getSession();
        $counter = (int)$session->get('session_counter');
        $counter++;
        return [
            '#markup' => $this->t('<div id="website-cart">cart3</div>'),
            '#attached' => [
                'library' => ['website_cart/website-cart']
            ]
        ];
    }

    public function getCacheMaxAge()
    {
        return 0;
    }
}