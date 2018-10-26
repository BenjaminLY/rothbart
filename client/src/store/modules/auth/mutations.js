const mutations = {
	UPDATE_TOKENS (state, value) {
		state.tokens = value
		window.localStorage.setItem('tokens', JSON.stringify(value))
	},
	UPDATE_NEXT_ROUTE (state, value) {
		state.nextRoute = value
	}
}

export default mutations
