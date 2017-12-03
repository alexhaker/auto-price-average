<?php
namespace AutoRIABundle\Form\EventListener;

use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class AddBodyStyleFieldSubscriber implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::PRE_SUBMIT => 'preSubmit'
		);
	}

	private function addBodyStyleField(FormInterface $form, $categoryId) {
		$formOptions = array(
			'class' => 'AutoRIABundle:BodyStyle',
			'empty_value' => 'Select body style',
			'label' => 'Body style',
			'query_builder' => function (ObjectManager $repository) use ($categoryId) {
				$qb = $repository->createQueryBuilder('BodyStyle')
					->field('id')->equals(new ObjectId("572235e937ca63bc668b6052"));

				return $qb;
			}
		);

		$form->add('bodyStyle', DocumentType::class, $formOptions);
	}

	public function preSetData(FormEvent $event) {
		$data = $event->getData();
		$form = $event->getForm();

		if (null === $data) {
			return;
		}

		$categoryId = $data->getCategory()->getId();

		$this->addBodyStyleField($form, $categoryId);
	}

	public function preSubmit(FormEvent $event) {
		$data = $event->getData();
		$form = $event->getForm();

		if (null === $data) {
			return;
		}

		$categoryId = $data->getCategory()->getId();

		$this->addBodyStyleField($form, $categoryId);
	}
}