<?php declare(strict_types=1);

namespace EAdmin\Core\Controller;

use EAdmin\Core\Component\ComponentInterface;
use EAdmin\Core\Component\ComponentTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ComponentController extends AbstractController implements ComponentInterface {
    use ComponentTrait;
}