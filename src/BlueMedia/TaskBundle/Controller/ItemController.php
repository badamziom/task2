<?php

namespace BlueMedia\TaskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class ItemController extends Controller {

    /**
     * @Route("/", name="item_index")
     * @Template()
     */
    public function indexAction() {

        $items = $this->getItems();
        return array('items' => $items);
    }

    /**
     * @Route("/item/{id}", name="get_item")
     * @Template("BlueMediaTaskBundle:Item:item.html.twig")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function getItemAction(Request $request) {

        $id = $request->get('id');

        $restClient = $this->container->get('ci.restclient');
        $data = $restClient->get($this->container->getParameter('path_to_data') . '/items/' . $id);
        $item = json_decode($data->getContent(), TRUE);

        return array('item' => $item);
    }

    /**
     * @Route("/instock", name="in_stock")
     * @Template("BlueMediaTaskBundle:Item:in_stock.html.twig")
     */
    public function inStockAction() {
        $params = array(
            'inStock=true'
        );
        $items = $this->getItems($params);
        return array('items' => $items);
    }

    /**
     * @Route("/notinstock", name="not_in_stock")
     * @Template("BlueMediaTaskBundle:Item:not_in_stock.html.twig")
     */
    public function notInStockAction() {
        $params = array(
            'inStock=false'
        );
        $items = $this->getItems($params);
        return array('items' => $items);
    }

    /**
     * @Route("/morethanfiveitems", name="more_than_five_items")
     * @Template("BlueMediaTaskBundle:Item:more_than_five.html.twig")
     */
    public function moreThanFiveItemsAction() {
        $params = array(
            'minStock=5'
        );
        $items = $this->getItems($params);

        return array('items' => $items);
    }

    public function getItems($params = array()) {
        $restClient = $this->container->get('ci.restclient');
        if (count($params) > 0) {
            $parameters = '?' . implode(' &  ', $params);
        } else {
            $parameters = '';
        }
        if ($data = $restClient->get($this->container->getParameter('path_to_data') . '/items' . $parameters)) {
            return json_decode($data->getContent(), TRUE);
        } else {
            $this->addFlash('error', 'Problem z pobraniem danych');
            return;
        }
    }

}
