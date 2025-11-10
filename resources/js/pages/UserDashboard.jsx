import { useState } from "react";
import Navbar from "../components/Layout/Navbar.jsx";
import UserSidebar from "../components/Layout/UserSidebar.jsx";
import UserBorrowing from "../components/Dashboard/UserBorrowing.jsx";

export default function UserDashboard() {
    const [activeTab, setActiveTab] = useState("borrowing");

    const renderTab = () => {
        switch(activeTab) {
            case "borrowing": return <UserBorrowing />;
            default: return <UserBorrowing />;
        }
    };

    return (
        <div className="flex min-h-screen bg-neutral-300">
            <UserSidebar activeTab={activeTab} setActiveTab={setActiveTab}/>
            <div className="flex-1 flex flex-col">
                <Navbar/>
                <main className="flex-1 p-8">{renderTab()}</main>
            </div>
        </div>
    );
}
