const mutations = {
	UPDATE (state, value) {
		state.data = value
		window.localStorage.setItem('rightAccess', JSON.stringify(value))
	}
}

export default mutations
