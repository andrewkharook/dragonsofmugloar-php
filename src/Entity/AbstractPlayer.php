<?php

namespace DragonsOfMugloar\Entity;


use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractPlayer
{
    /**
     * Sum of all the skills
     */
    const SKILLS_TOTAL = 20;

    /**
     * Associative array of character's skills
     *
     * @var array {
     *      @var int $attack
     *      @var int $armor
     *      @var int $agility
     *      @var int $endurance
     * }
     */
    protected $skills;

    /**
     * Constructor.
     *
     * @param array $skills
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @throws \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function __construct(array $skills = []) {
        $resolver = new OptionsResolver();
        $this->configureSkills($resolver);

        $this->skills = $resolver->resolve($skills);
    }

    /**
     * Validates skills
     *
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    protected function configureSkills(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'attack',
            'armor',
            'agility',
            'endurance',
        ]);
    }

    /**
     * Getter method for player's skills
     *
     * @return array
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * Magic method for getting a skill
     *
     * @param string $name
     * @return int|null
     */
    public function __get($name): ?int
    {
        if (array_key_exists($name, $this->skills)) {
            return $this->skills[$name];
        }

        return null;
    }

    /**
     * Magic method for setting a skill
     *
     * @param string $name
     * @param int $value
     */
    public function __set($name, $value): void
    {
        $this->skills[$name] = $value;
    }

    /**
     * Magic method for checking if a skill exists
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return isset($this->skills[$name]);
    }

    /**
     * Dumps the object to json
     *
     * @return string
     */
    abstract public function __toString(): string;
}
