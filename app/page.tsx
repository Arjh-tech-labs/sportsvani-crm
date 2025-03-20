import Link from "next/link"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { ChevronRight, Shield, Users, Trophy, Calendar, BarChart2 } from "lucide-react"

export default function HomePage() {
  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      {/* Hero Section */}
      <header className="bg-blue-600 text-white">
        <div className="container mx-auto px-4 py-16 md:py-24">
          <div className="flex flex-col md:flex-row items-center justify-between">
            <div className="md:w-1/2 mb-10 md:mb-0">
              <h1 className="text-4xl md:text-5xl font-bold mb-4">SportsVani</h1>
              <p className="text-xl md:text-2xl mb-8">
                The ultimate cricket management platform for players, teams, and tournaments
              </p>
              <div className="flex flex-col sm:flex-row gap-4">
                <Button size="lg" className="bg-white text-blue-600 hover:bg-blue-50">
                  Register Now
                </Button>
                <Link href="/admin/login">
                  <Button size="lg" variant="outline" className="border-white text-white hover:bg-blue-700">
                    Admin Login
                  </Button>
                </Link>
                <Link href="/superadmin/login">
                  <Button size="lg" variant="outline" className="border-white text-white hover:bg-blue-700">
                    Super Admin Login
                  </Button>
                </Link>
              </div>
            </div>
            <div className="md:w-1/2">
              <img src="/placeholder.svg?height=400&width=500" alt="Cricket players" className="rounded-lg shadow-lg" />
            </div>
          </div>
        </div>
      </header>

      {/* Features Section */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold text-center mb-12">Platform Features</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <FeatureCard
              icon={<Users className="h-10 w-10 text-blue-500" />}
              title="Player Management"
              description="Create detailed player profiles with statistics, match history, and performance analytics."
            />
            <FeatureCard
              icon={<Shield className="h-10 w-10 text-blue-500" />}
              title="Team Management"
              description="Create and manage teams, add players, track team statistics, and organize matches."
            />
            <FeatureCard
              icon={<Trophy className="h-10 w-10 text-blue-500" />}
              title="Tournament Organization"
              description="Create tournaments with customizable formats, schedules, and leaderboards."
            />
            <FeatureCard
              icon={<Calendar className="h-10 w-10 text-blue-500" />}
              title="Match Scheduling"
              description="Schedule matches, manage officials, and track match details and results."
            />
            <FeatureCard
              icon={<BarChart2 className="h-10 w-10 text-blue-500" />}
              title="Live Scoring"
              description="Real-time match scoring with detailed statistics and wagon wheel visualization."
            />
            <FeatureCard
              icon={<ChevronRight className="h-10 w-10 text-blue-500" />}
              title="Live Streaming"
              description="Stream matches live on YouTube directly from the platform with integrated APIs."
            />
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="bg-blue-50 py-16">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-3xl font-bold mb-6">Ready to get started?</h2>
          <p className="text-xl mb-8 max-w-2xl mx-auto">
            Join thousands of cricket enthusiasts who are already using SportsVani to manage their cricket journey.
          </p>
          <div className="flex flex-col sm:flex-row justify-center gap-4">
            <Button size="lg" className="bg-blue-600 text-white hover:bg-blue-700">
              Register Now
            </Button>
            <Link href="/superadmin/login">
              <Button size="lg" variant="outline" className="border-blue-600 text-blue-600 hover:bg-blue-50">
                Super Admin Login
              </Button>
            </Link>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-gray-800 text-white py-12">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
              <h3 className="text-xl font-bold mb-4">SportsVani</h3>
              <p className="mb-4">The ultimate cricket management platform</p>
            </div>
            <div>
              <h3 className="text-xl font-bold mb-4">Quick Links</h3>
              <ul className="space-y-2">
                <li>
                  <Link href="/" className="hover:text-blue-300">
                    Home
                  </Link>
                </li>
                <li>
                  <Link href="/features" className="hover:text-blue-300">
                    Features
                  </Link>
                </li>
                <li>
                  <Link href="/register" className="hover:text-blue-300">
                    Register
                  </Link>
                </li>
                <li>
                  <Link href="/superadmin/login" className="hover:text-blue-300">
                    Super Admin Login
                  </Link>
                </li>
              </ul>
            </div>
            <div>
              <h3 className="text-xl font-bold mb-4">Contact</h3>
              <p className="mb-2">Email: info@sportsvani.in</p>
              <p>Phone: +91 1234567890</p>
            </div>
          </div>
          <div className="border-t border-gray-700 mt-8 pt-8 text-center">
            <p>&copy; {new Date().getFullYear()} SportsVani. All rights reserved.</p>
          </div>
        </div>
      </footer>
    </div>
  )
}

function FeatureCard({ icon, title, description }) {
  return (
    <Card className="h-full">
      <CardHeader>
        <div className="mb-4">{icon}</div>
        <CardTitle>{title}</CardTitle>
      </CardHeader>
      <CardContent>
        <CardDescription className="text-base">{description}</CardDescription>
      </CardContent>
      <CardFooter>
        <Button variant="ghost" className="text-blue-600 hover:text-blue-800 p-0">
          Learn more <ChevronRight className="h-4 w-4 ml-1" />
        </Button>
      </CardFooter>
    </Card>
  )
}

