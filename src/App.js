import './App.css';
import Login from './pages/Login';
import Register from './pages/Register';
import Courses from './pages/Courses';
import { BrowserRouter, Routes, Route } from "react-router-dom";

function App() {
  return (
    <div className="App">
      <BrowserRouter>
      <Routes>
        <Route path='/' element= {<Login />}/>
        <Route path='/register' element={<Register />} />
        <Route path='/courses' element={<Courses />} />
      </Routes>
      </BrowserRouter>
    </div>
  );
}

export default App;
