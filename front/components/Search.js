const Search = ({ onSearch }) => {
  return <form onSubmit={(e) => e.preventDefault()}>
    <div className='form-container'>
      <input type='text' name='search' onChange={(event) => onSearch(event.target.value)} placeholder='Search' />
    </div>
  </form>
}

export default Search
