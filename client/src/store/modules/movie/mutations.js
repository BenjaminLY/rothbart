const mutations = {
	UPDATE (state, value) {
		state.data = value
		window.localStorage.setItem('movie', JSON.stringify(value))
	}
}

export default mutations
