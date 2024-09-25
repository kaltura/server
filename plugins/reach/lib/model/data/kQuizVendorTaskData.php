<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kQuizVendorTaskData extends kVendorTaskData
{
	public int $numberOfQuestions = 0;
	public string $questionsType = null;
	public string $context = "";
	public ?string $quizOutput = null;

	public function getNumberOfQuestions(): int
	{
		return $this->numberOfQuestions;
	}

	public function setNumberOfQuestions(int $numberOfQuestions): void
	{
		$this->numberOfQuestions = $numberOfQuestions;
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

	public function setQuizOutput(?string $quizOutput): void
	{
		$this->quizOutput = $quizOutput;
	}
}
