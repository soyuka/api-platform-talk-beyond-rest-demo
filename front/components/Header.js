import Link from 'next/link'
import BookmarkCreator from './BookmarkCreator.js'
import Search from './Search.js'

const Header = ({ onSearch, onCreate }) => (
  <header className='row'>
    <div className='col-quarter block'>
      <Link href={`/`}><a><button className='reverse'>Home</button></a></Link>
    </div>
    <div className='col-half block'>
      <Search onSearch={onSearch} />
    </div>
    <div className='col-quarter block'>
      <BookmarkCreator onCreate={onCreate} />
    </div>
  </header>
)

export default Header
