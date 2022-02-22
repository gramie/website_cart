<?php

namespace Drupal\website_cart\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

define('CART_SESSION_VARIABLE', 'website_cart');

/**
 * Defines WebsiteCartController class
 */
class WebsiteCartController extends ControllerBase {

    public function getContents() {
        $request = \Drupal::request();
        $session = $request->getSession();
        $cart = $session->get(CART_SESSION_VARIABLE);
        if (!$cart) {
            $cart = $this->newCart();
            $session->set(CART_SESSION_VARIABLE, $cart);
        }
        return new JsonResponse([
            'status' => 0,
            'cart' => $cart,
        ]);
    }

    protected function newCart() {
        return [
            'items' => [],
        ];
    }

    public function addItemToCart() {
        $request = \Drupal::request();

        try {
            $item_id = $request->query->get('id');
            $item_quantity = $request->query->get('quantity');
            $cart = $this->loadCart();
            if ($item_quantity > 10) {
                throw new \Exception('Unable to add more than 10 items at one time');
            }

            $cart = $this->addItem($cart, $item_id, $item_quantity);
            $this->saveCart($cart);

            $status = 0;
            $status_message = 'OK';
        } catch (\Exception $e) {
            $status = 1;
            $status_message = $e->getMessage();
        }
        return new JsonResponse([
            'status' => $status,
            'status_message' => $status_message,
            'cart' => $cart,
        ]);
    }

    /**
     * Remove all the items of a given item ID from the cart
     *
     * @param integer $item_id
     * @return void
     */
    public function removeItemFromCart() {
        $request = \Drupal::request();
        $item_id = $request->query->get('id');

        $cart = $this->loadCart();

        $result = [
            'status' => 1,
            'status_message' => 'Item not found',
        ];

        foreach ($cart['items'] as $key => $item) {
            if ($item['item_id'] == $item_id) {
                $result['status'] = 0;
                $result['status_message'] = 'Item removed';
                unset($cart['items'][$key]);
                $cart['items'] = array_values($cart['items']);
                break;
            }
        }

        $this->saveCart($cart);
        $result['cart'] = $cart;

        return new JsonResponse($result);
    }

    protected function loadCart() {
        $request = \Drupal::request();
        $session = $request->getSession();
        return $session->get(CART_SESSION_VARIABLE);
    }

    protected function saveCart(array $cart) {
        $request = \Drupal::request();
        $session = $request->getSession();
        $session->set(CART_SESSION_VARIABLE, $cart);
    }

    protected function addItem(array $cart, int $id, int $quantity) : array {
        $node = Node::load($id);
        if (!$node) {
            throw new \Exception("Unable to find item with ID $id");
        }

        $added = false;
        foreach ($cart['items'] as $key => &$item) {
            if ($item['item_id'] == $id) {
                $item['quantity'] += $quantity;
                if ($item['quantity'] < 0) {
                    $item['quantity'] = 0;
                }
                $added = true;
                break;
            }
        }
        if (!$added) {
            $cart['items'][] = [
                'item_id' => $id,
                'quantity' => $quantity,
                'title' => $node->title->value,
                'unit_size' => $node->get('field_unit_size')->getValue()[0]['value'],
                'unit_price' => $node->get('field_unit_price')->getValue()[0]['value'],
            ];
        }

        return $cart;
    }

}