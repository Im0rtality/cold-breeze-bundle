<?php

namespace Im0rtality\ColdBreezeBundle\Controller;

use Im0rtality\ColdBreezeBundle\Helper\Settings;
use Im0rtality\ColdBreezeBundle\Helper\Version;
use Im0rtality\ColdBreezeBundle\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestController extends Controller
{
    /**
     * @param Request    $request
     * @param string     $resource
     * @param string|int $id
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function getAction(Request $request, $resource, $id)
    {
        $repository = $this->getRepository($resource);

        /** @var Serializer $serializer */
        $serializer = $this->get('im0rtality_cold_breeze.serializer');
        $serializer->setExpands($this->getExpands($request));

        $instance = $repository->find($id);
        if (null === $instance) {
            throw new NotFoundHttpException('Resource could not be found');
        }
        return new JsonResponse(
            $serializer->serialize($instance)
        );
    }

    /**
     * @param Request $request
     * @param string  $resource
     * @return JsonResponse
     */
    public function listAction(Request $request, $resource)
    {
        $repository = $this->getRepository($resource);

        /** @var Serializer $serializer */
        $serializer = $this->get('im0rtality_cold_breeze.serializer');
        $serializer->setExpands($this->getExpands($request));

        return new JsonResponse(
            $serializer->serialize(
                $repository->findBy([], null, $request->query->get('limit', 10), $request->query->get('offset', 0))
            )
        );
    }

    public function settingsAction()
    {
        /** @var Settings $helper */
        $helper = $this->get('im0rtality_cold_breeze.helper.settings');
        $data   = [
            'imagine' => $helper->getImagineSettings(),
        ];
        return new JsonResponse($data);
    }

    public function versionAction()
    {
        /** @var Version $helper */
        $helper = $this->get('im0rtality_cold_breeze.helper.version');
        return new JsonResponse(['version' => $helper->getVersion()]);
    }

    /**
     * @param $resource
     * @return object
     */
    protected function getRepository($resource)
    {
        $mapping = $this->get('kernel')->getContainer()->getParameter(
            'im0rtality_cold_breeze.resource_repository.mapping'
        );
        return $this->get(sprintf('sylius.repository.%s', $mapping[$resource]));
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getExpands(Request $request)
    {
        $expand = explode(',', $request->get('expand', ''));
        return $expand;
    }
} 
