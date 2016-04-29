<?php
namespace AutoRIABundle\Form\Type;

use AutoRIABundle\Form\EventListener\AddBodyStyleFieldSubscriber;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateSearchType extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('Category', DocumentType::class, array(
				'class' => 'AutoRIABundle:Category',
				'choice_label' => 'name',
				'empty_value' => 'Select category',
				'attr' => array(
					//'onchange' => 'document.getElementsByName("create_search")[0].submit()'
				)
			))
		;

		$builder
			->addEventSubscriber(new AddBodyStyleFieldSubscriber())
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class' => 'AutoRIABundle\Document\Search',
			)
		);
	}

	public function getName()
	{
		return 'create_search';
	}
}