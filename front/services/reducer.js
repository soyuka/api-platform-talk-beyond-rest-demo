function reducer (state, action) {
  console.log(action.type, state, action)

  switch (action.type) {
    case 'remove': {
      const bookmarks = [].slice.call(state.bookmarks)
      const index = bookmarks.findIndex(e => e.id === action.id)
      if (!~index) { return }
      bookmarks.splice(index, 1)
      return { bookmarks }
    }

    case 'change': {
      const bookmarks = [].slice.call(state.bookmarks)
      const index = bookmarks.findIndex(e => e.id === action.bookmark.id)

      if (!~index) {
        bookmarks.push(action.bookmark)
        return { bookmarks }
      }

      bookmarks[index] = action.bookmark
      return { bookmarks }
    }

    case 'init': {
      return { bookmarks: action.bookmarks }
    }

    case 'filter': {
      if (action.filtered === null) {
        return {
          bookmarks: [].slice.call(state.bookmarks).map((bookmark) => {
            bookmark.visible = true
            return bookmark
          })
        }
      }

      const bookmarks = [].slice.call(state.bookmarks).map((bookmark) => {
        const index = action.filtered.findIndex(e => e.id === bookmark.id)
        bookmark.visible = !!~index
        return bookmark
      })

      return { bookmarks }
    }

    default:
      return state
  }
}

export default reducer
