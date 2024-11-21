import './App.css';
import Login from './pages/Login';
import Register from './pages/Register';
import Courses from './pages/Courses';
import { BrowserRouter ,Routes, Route } from "react-router-dom";
import Streams from './pages/Streams';
import Assignment from './pages/Assignment';
import Admin from './pages/Admin'
import Instructor from './pages/Instructor';

function App() {
  return (
    <div className="App">
      <BrowserRouter>
      <Routes>
        <Route path='/' element= {<Login />}/>
        <Route path='/register' element={<Register />} />
        <Route path='/courses' element={<Courses />} />
        <Route path="/streams/:courseId" element={<Streams />} />
        <Route path="/assignment/:assignment_id" element={<Assignment />}/>
        <Route path='/admin' element={<Admin />} />
        <Route path='/instructor' element={<Instructor/>}/>
      </Routes>
      </BrowserRouter>
    </div>
  );
}

export default App;
