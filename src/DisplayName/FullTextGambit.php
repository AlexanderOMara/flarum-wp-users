<?php

namespace AlexanderOMara\FlarumWPUsers\DisplayName;

use Flarum\Search\GambitInterface;
use Flarum\Search\SearchState;
use Flarum\User\UserRepository;
use Flarum\User\LoginProvider;
use Illuminate\Database\Eloquent\Builder;

use AlexanderOMara\FlarumWPUsers\Core;

/**
 * FullTextGambit class.
 */
class FullTextGambit implements GambitInterface {
	/**
	 * User repository.
	 *
	 * @var UserRepository
	 */
	protected /*UserRepository*/ $users;

	/**
	 * Core object.
	 *
	 * @var Core
	 */
	protected /*Core*/ $core;

	/**
	 * FullTextGambit class.
	 *
	 * @param UserRepository User repository.
	 * @param Core Core object.
	 */
	public function __construct(UserRepository $users, Core $core) {
		$this->users = $users;
		$this->core = $core;
	}

	/**
	 * Get user WordPress sub-query.
	 *
	 * @param string $bit The piece of the search string.
	 * @return Builder|null Builder instance or null if none.
	 */
	protected function getUserWordPressSubQuery($bit): ?Builder {
		// Search for matching WordPress display names, if any.
		$wpUsers = $this->core->displayNameSearch($bit);
		if (!$wpUsers) {
			return null;
		}
		return LoginProvider::query()->select('user_id')
			->where('provider', Core::ID)
			->whereIn('identifier', $wpUsers);
	}

	/**
	 * Get user search sub-query.
	 *
	 * @param string $bit The piece of the search string.
	 * @return Builder Builder instance.
	 */
	protected function getUserSearchSubQuery($bit): Builder {
		// Base query used in:
		// Flarum\User\Search\Gambit\FulltextGambit->getUserSearchSubQuery
		$query = $this->users->query()->select('id')
			->where('username', 'like', "{$bit}%");

		// If display name driver is enabled, extend query.
		if ($this->core->displayNameDriverEnabled()) {
			$subQuery = $this->getUserWordPressSubQuery($bit);
			if ($subQuery) {
				$query = $query->orWhereIn('id', $subQuery);
			}
		}
		return $query;
	}

	/**
	 * {@inheritdoc}
	 */
	public function apply(SearchState $search, $bit) {
		$search->getQuery()->whereIn('id', $this->getUserSearchSubQuery($bit));
	}
}
