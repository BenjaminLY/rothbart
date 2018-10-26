const mutations = {
	UPDATE_SESSION_STORAGE_DATA (state, value) {
		state.localData = value
		window.sessionStorage.setItem('data', JSON.stringify(value))
	},
	UPDATE_DATA (state, value) {
		state.data = value
	},
	UPDATE_SHARE (state, value) {
		state.share = value
	}
}

export default mutations
