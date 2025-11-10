import {createContext, useContext, useEffect, useState} from "react";
import api from "../api/api";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
    // Initialize user from localStorage if it exists
    const [user, setUser] = useState(() => {
        const savedUser = localStorage.getItem("user");
        if (savedUser && savedUser !== "undefined") {
            try {
                return JSON.parse(savedUser);
            } catch (err) {
                console.error("Error parsing user from localStorage:", err);
                return null;
            }
        }
        return null;
    });

    // Save token in localStorage
    useEffect(() => {
        const token = localStorage.getItem("token");
        if (token) {
            api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        }
    }, []);

    const login = async (email, password) => {
        const response = await api.post("/login", { email, password });

        const loggedUser = response.data.data.user;
        const token = response.data.data.token;

        localStorage.setItem("token", token);
        localStorage.setItem("user", JSON.stringify(loggedUser));

        setUser(loggedUser);
        api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    };

    const logout = async () => {
        await api.post("/logout");
        setUser(null);
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        delete api.defaults.headers.common["Authorization"];
    };

    return (
        <AuthContext.Provider value={{ user, login, logout }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => useContext(AuthContext);
