<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kQuizVendorTaskData extends kVendorTaskData
{
	public int $numberOfQuestion= 0;
	public string $questionsType = null;
	public string $context = "";

	public function getNumberOfQuestion(): int
	{
		return $this->numberOfQuestion;
	}

	public function setNumberOfQuestion(int $numberOfQuestion): void
	{
		$this->numberOfQuestion = $numberOfQuestion;
	}

	public function getQuestionsType(): string
	{
		return $this->questionsType;
	}

	public function setQuestionsType(string $questionsType): void
	{
		$this->questionsType = $questionsType;
	}

	public function getContext(): string
	{
		return $this->context;
	}

	public function setContext(string $context): void
	{
		$this->context = $context;
	}
}
