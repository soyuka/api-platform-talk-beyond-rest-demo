import '../style.css'
import Layout from '../components/Layout'
import Bookmark from '../components/Bookmark'
import Bookmarks from '../services/bookmarks'
import React, { Fragment, useReducer, useEffect, useState } from 'react'
import reducer from '../services/reducer'

const DELIMITER = 5
const recursivity = ({ bookmarks, onRemove }) => {
  if (!bookmarks.length) {
    return null
  }

  return (
    <Fragment>
      <div className='row'>
        {bookmarks.slice(0, DELIMITER).map(bookmark => (
          <Bookmark key={bookmark.id} bookmark={bookmark} onRemove={onRemove} />
        ))}
      </div>
      <List bookmarks={bookmarks.slice(DELIMITER)} />
    </Fragment>
  )
}

const List = (args) => {
  return recursivity(args)
}

function Index () {
  const [state, dispatch] = useReducer(reducer, { bookmarks: [] })
  const [error, setError] = useState(null)
  let timeout = null

  const onError = (error) => {
    setError(error)

    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(() => {
      setError(null)
    }, 3000)
  }

  const onRemove = async (id) => {
    try {
      await Bookmarks.remove(id)
    } catch (error) {
      onError(error.message)
      return
    }

    dispatch({ type: 'remove', id })
  }

  const onCreate = async (link, cb) => {
    try {
      const res = await Bookmarks.create(link)
      cb(res)
    } catch (error) {
      onError(error.message)
    }
  }

  const onSearch = async (term) => {
    const res = await Bookmarks.search(term)
    dispatch({ type: 'filter', filtered: res })
  }

  useEffect(() => {
    Bookmarks.subscribe({
      init: (bookmarks) => {
        dispatch({ type: 'init', bookmarks })
      },
      next: ({ bookmark }) => {
        dispatch({ type: 'change', bookmark })
      }
    })

    return function cleanup () {
      Bookmarks.unsubscribe()
    }
  }, [])

  return <Layout className='container' error={error} onCreate={onCreate} onSearch={onSearch} >
    <List bookmarks={state.bookmarks.filter(bookmark => bookmark.visible !== false)} onRemove={onRemove} />
  </Layout>
}

export default Index
