<?php

/**
 * @package plugins.reach
 * @subpackage model
 */
class kQuizVendorTaskData extends kLocalizedVendorTaskData
{
	public $numberOfQuestions = 0;
	public $questionsType = null;
	public $context = "";
	public $formalStyle = null;
	public $createQuiz = True;
	public $quizOutput = null;

	public function getNumberOfQuestions(): int
	{
		return $this->numberOfQuestions;
	}

	public function setNumberOfQuestions(int $numberOfQuestions): void
	{
		$this->numberOfQuestions = $numberOfQuestions;
	}

	public function getQuestionsType(): ?string
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

	public function getFormalStyle(): ?string
	{
		return $this->formalStyle;
	}

	public function setFormalStyle(string $formalStyle): void
	{
		$this->formalStyle = $formalStyle;
	}

	public function getCreateQuiz(): bool
	{
		return $this->createQuiz;
	}

	public function setCreateQuiz(bool $createQuiz): void
	{
		$this->createQuiz = $createQuiz;
	}

	public function getQuizOutput(): ?string
	{
		return $this->quizOutput;
	}

	public function setQuizOutput(?string $quizOutput): void
	{
		$this->quizOutput = $quizOutput;
	}
}
