import { useState } from 'react'

const BookmarkCreator = ({ onCreate }) => {
  const [value, setValue] = useState('')

  const submit = async (event) => {
    event.preventDefault()
    onCreate(value, (link) => {
      setValue(link)
    })
  }

  return (
    <form onSubmit={submit}>
      <div className='form-container'>
        <input type='text' name='link' value={value} onChange={e => setValue(e.currentTarget.value)} placeholder='https://example.com' />
        <button type='submit'>Bookmark</button>
      </div>
    </form>
  )
}

export default BookmarkCreator
