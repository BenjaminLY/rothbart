const mutations = {
	UPDATE_USER (state, value) {
		state.user = value
		window.localStorage.setItem('user', JSON.stringify(value))
	}
}

export default mutations
